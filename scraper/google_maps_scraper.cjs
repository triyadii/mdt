const fs = require('fs');
const os = require('os');
const path = require('path');



const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');

puppeteer.use(StealthPlugin());

// Parse CLI Arguments
const args = process.argv.slice(2);
function getArg(key, defaultValue) {
    const prefix = `--${key}=`;
    const found = args.find(a => a.startsWith(prefix));
    if (found) return found.substring(prefix.length);
    return defaultValue;
}

const keyword = getArg('keyword', 'cafe');
const location = getArg('location', 'Medan');
const districtArg = getArg('district', '');
const limit = parseInt(getArg('limit', '30'), 10);
const taskId = getArg('taskId', null);
const defaultChrome = fs.existsSync('/opt/google/chrome/chrome')
    ? '/opt/google/chrome/chrome'
    : '/usr/bin/google-chrome';
const chromePath = getArg('chromePath', defaultChrome);

// Detect target district from argument or location string
const locationParts = location.split(',').map(s => s.trim());
const targetDistrict = districtArg || (locationParts.length >= 3 && !locationParts[0].toLowerCase().includes('semua kecamatan') ? locationParts[0] : '');

// Custom Dynamic Filters
const filterNoWebsite = getArg('filterNoWebsite', 'true') === 'true';
const filterHasPhone = getArg('filterHasPhone', 'true') === 'true';
const minReviews = parseInt(getArg('minReviews', '5'), 10);
const minRating = parseFloat(getArg('minRating', '0'));

function matchesDistrict(address, district) {
    if (!district) return true;
    const cleanDistrict = district.replace(/^(kecamatan|kec\.)\s+/i, '').trim().toLowerCase();
    if (!cleanDistrict || cleanDistrict === 'semua kecamatan') return true;

    const normDistrict = cleanDistrict.replace(/[^a-z0-9]/g, ' ').trim();
    const normAddress = (address || '').toLowerCase().replace(/[^a-z0-9]/g, ' ');

    return normAddress.includes(normDistrict);
}

function emit(type, data) {
    console.log(JSON.stringify({ type, timestamp: new Date().toISOString(), ...data }));
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Random delay between min and max ms (default 5000 - 7000 ms)
function randomDelay(min = 5000, max = 7000) {
    const ms = Math.floor(Math.random() * (max - min + 1)) + min;
    return sleep(ms);
}

function cleanText(str) {
    if (!str) return '';
    return str.replace(/[\uE000-\uF8FF\u200B-\u200D\uFEFF]/g, '').trim();
}

function normalizePhoneNumber(phone) {
    if (!phone) return null;
    let clean = cleanText(phone).replace(/[^\d+]/g, '');
    if (clean.startsWith('+62')) {
        clean = '62' + clean.substring(3);
    } else if (clean.startsWith('62')) {
        // already 62
    } else if (clean.startsWith('0')) {
        clean = '62' + clean.substring(1);
    } else if (clean.startsWith('+')) {
        clean = clean.substring(1);
    }
    // Only accept numbers that look like Indonesian mobile/fixed (9-15 digits)
    if (clean.length >= 9 && clean.length <= 16) {
        return clean;
    }
    return null;
}

function isSocialOrDirectory(url) {
    if (!url) return false;
    const lower = url.toLowerCase();
    const socialDomains = [
        'instagram.com',
        'facebook.com',
        'fb.com',
        'tiktok.com',
        'linktr.ee',
        'wa.me',
        'whatsapp.com',
        'g.page',
        'google.com',
        'maps.google.com'
    ];
    return socialDomains.some(d => lower.includes(d));
}

async function run() {
    emit('status', { message: `Memulai scraper untuk "${keyword}" di "${location}" (Maksimal target: ${limit} leads)...` });

    const runnerDir = fs.mkdtempSync(path.join('/tmp', 'gmaps_runner_'));
    const runnerConfig = path.join(runnerDir, 'config');
    const runnerCache = path.join(runnerDir, 'cache');
    const runnerData = path.join(runnerDir, 'data');
    const runnerProfile = path.join(runnerDir, 'profile');
    const runnerCrash = path.join(runnerDir, 'crash');

    fs.mkdirSync(runnerConfig, { recursive: true });
    fs.mkdirSync(runnerCache, { recursive: true });
    fs.mkdirSync(runnerData, { recursive: true });
    fs.mkdirSync(runnerProfile, { recursive: true });
    fs.mkdirSync(runnerCrash, { recursive: true });

    process.env.HOME = runnerDir;
    process.env.XDG_CONFIG_HOME = runnerConfig;
    process.env.XDG_CACHE_HOME = runnerCache;
    process.env.XDG_DATA_HOME = runnerData;

    let browser = null;

    try {
        browser = await puppeteer.launch({
            executablePath: chromePath,
            headless: 'new',
            userDataDir: runnerProfile,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-accelerated-2d-canvas',
                '--no-first-run',
                '--no-zygote',
                '--disable-gpu',
                '--disable-crash-reporter',
                `--crash-dumps-dir=${runnerCrash}`,
                '--lang=id-ID,id,en-US,en'
            ],
            defaultViewport: { width: 1366, height: 768 }
        });

        const page = await browser.newPage();
        await page.setExtraHTTPHeaders({
            'Accept-Language': 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7'
        });

        const cleanDistName = targetDistrict.replace(/^(kecamatan|kec\.)\s+/i, '').trim();
        const searchQuery = targetDistrict
            ? `${keyword} "${cleanDistName}" ${locationParts.slice(1).join(' ')}`.trim()
            : `${keyword} ${location}`;
        const mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(searchQuery)}?hl=id`;

        emit('status', { message: `Membuka Google Maps: ${mapsUrl}` });
        await page.goto(mapsUrl, { waitUntil: 'networkidle2', timeout: 60000 });

        // Tunggu feed hasil muncul
        await page.waitForSelector('div[role="feed"], div[role="main"]', { timeout: 15000 }).catch(() => null);

        // Infinite scroll di container hasil
        emit('status', { message: 'Mengumpulkan daftar tempat dari pencarian...' });
        
        let previousHeight = 0;
        let placeUrls = new Set();
        let scrollAttempts = 0;
        const maxScrollAttempts = 15;
        const targetCandidates = Math.max(limit * 4, 30);

        while (scrollAttempts < maxScrollAttempts && placeUrls.size < targetCandidates) {
            // Ambil semua link tempat yang tampak
            const links = await page.evaluate(() => {
                const anchors = Array.from(document.querySelectorAll('a[href*="/maps/place/"]'));
                return anchors.map(a => a.href).filter(href => href && href.includes('/maps/place/'));
            });

            links.forEach(l => placeUrls.add(l));

            // Cek apakah sudah ada tulisan "Anda telah mencapai akhir daftar"
            const endReached = await page.evaluate(() => {
                const text = document.body.innerText || '';
                return text.includes('Anda telah mencapai akhir daftar') || text.includes("You've reached the end of the list");
            });

            if (endReached) {
                emit('status', { message: 'Akhir daftar hasil Google Maps tercapai.' });
                break;
            }

            // Scroll container
            await page.evaluate(() => {
                const feed = document.querySelector('div[role="feed"]');
                if (feed) {
                    feed.scrollTop += 1200;
                } else {
                    window.scrollBy(0, 1000);
                }
            });

            await sleep(2000);
            scrollAttempts++;
        }

        const uniquePlaceUrls = Array.from(placeUrls);
        emit('status', { message: `Berhasil mendata ${uniquePlaceUrls.length} calon tempat. Memulai ekstraksi detail dengan delay 5-7 detik...` });

        let qualifiedCount = 0;

        for (let i = 0; i < uniquePlaceUrls.length; i++) {
            if (qualifiedCount >= limit) {
                emit('status', { message: `Target limit ${limit} leads tercapai. Mengakhiri proses.` });
                break;
            }

            const currentUrl = uniquePlaceUrls[i];
            const remainingPlaces = Math.max(0, uniquePlaceUrls.length - (i + 1));
            const etaSec = remainingPlaces * 6;
            const percent = Math.min(99, Math.round(((i + 1) / uniquePlaceUrls.length) * 100));

            emit('progress', {
                index: i + 1,
                total: uniquePlaceUrls.length,
                qualified: qualifiedCount,
                percent: percent,
                etaSeconds: etaSec,
                step: `Memeriksa tempat ${i + 1} dari ${uniquePlaceUrls.length} (${qualifiedCount}/${limit} UMKM ditemukan)`,
                url: currentUrl
            });

            try {
                await page.goto(currentUrl, { waitUntil: 'domcontentloaded', timeout: 30000 });
                // Tunggu elemen nama tempat muncul
                await page.waitForSelector('h1', { timeout: 10000 }).catch(() => null);

                // Ekstraksi data tempat
                const placeData = await page.evaluate(() => {
                    const name = document.querySelector('h1')?.innerText?.trim() || '';
                    
                    // Kategori
                    const categoryBtn = document.querySelector('button[jsaction*="category"]');
                    const category = categoryBtn ? categoryBtn.innerText.trim() : '';

                    // Rating & Review
                    let rating = null;
                    let reviewCount = 0;
                    
                    // Cari rating
                    const ratingSpan = document.querySelector('span[role="img"][aria-label*="bintang"]') || 
                                       document.querySelector('span[role="img"][aria-label*="stars"]') ||
                                       document.querySelector('div.F7nice span[aria-hidden="true"]');
                    if (ratingSpan) {
                        const rText = ratingSpan.innerText || ratingSpan.getAttribute('aria-label') || '';
                        const match = rText.match(/(\d+[.,]\d+)/);
                        if (match) {
                            rating = parseFloat(match[1].replace(',', '.'));
                        }
                    }

                    // Cari review count
                    const reviewElements = Array.from(document.querySelectorAll('span, button'));
                    for (const el of reviewElements) {
                        const txt = el.innerText ? el.innerText.trim() : '';
                        const rMatch = txt.match(/\((\d[\d.,]*)\s*(ulasan|reviews)?\)/i);
                        if (rMatch) {
                            reviewCount = parseInt(rMatch[1].replace(/[.,]/g, ''), 10);
                            break;
                        }
                    }

                    // Website button / link
                    const websiteAnchor = document.querySelector('a[data-item-id="authority"]') ||
                                         document.querySelector('a[aria-label*="Situs web"]') ||
                                         document.querySelector('a[aria-label*="Website"]');
                    const websiteUrl = websiteAnchor ? websiteAnchor.getAttribute('href') : null;

                    // Phone button / text
                    let phone = null;
                    const phoneBtn = document.querySelector('button[data-tooltip*="Salin nomor telepon"]') ||
                                     document.querySelector('button[data-item-id*="phone:tel:"]') ||
                                     document.querySelector('button[aria-label*="Telepon"]');
                    if (phoneBtn) {
                        phone = phoneBtn.innerText?.trim() || phoneBtn.getAttribute('aria-label') || null;
                    }
                    if (!phone) {
                        // Cek selector tel:
                        const telAnchor = document.querySelector('a[href^="tel:"]');
                        if (telAnchor) {
                            phone = telAnchor.getAttribute('href')?.replace('tel:', '');
                        }
                    }
                    if (!phone) {
                        // Regex cari dari blok info
                        const bodyText = document.body.innerText;
                        const phoneMatch = bodyText.match(/(?:(?:\+62|62|0)8[1-9][0-9]{7,11}|\(061\)\s*[0-9]{6,8}|061\s*[0-9]{6,8})/);
                        if (phoneMatch) {
                            phone = phoneMatch[0];
                        }
                    }

                    // Address
                    const addressBtn = document.querySelector('button[data-tooltip*="Salin alamat"]') ||
                                      document.querySelector('button[data-item-id="address"]') ||
                                      document.querySelector('button[aria-label*="Alamat"]');
                    const address = addressBtn ? addressBtn.innerText?.trim() || addressBtn.getAttribute('aria-label') : '';

                    return {
                        name,
                        category,
                        rating,
                        reviewCount,
                        websiteUrl,
                        phone,
                        address
                    };
                });

                // Validasi Tempat
                if (!placeData.name) {
                    continue;
                }

                const cleanAddress = cleanText(placeData.address).replace(/^alamat[:\s]*/i, '').trim();

                // 0. Filter Kecamatan (District strict filtering)
                const passDistrict = matchesDistrict(cleanAddress, targetDistrict);

                // 1. Apakah punya website mandiri?
                const hasOwnWebsite = placeData.websiteUrl && !isSocialOrDirectory(placeData.websiteUrl);
                
                // 2. Normalisasi no WA
                const phoneWa = normalizePhoneNumber(placeData.phone);

                // 3. Review count
                const reviewCount = placeData.reviewCount || 0;

                // FILTER CRITERIA DINAMIS
                const passWebsite = !filterNoWebsite || !hasOwnWebsite;
                const passPhone = !filterHasPhone || (phoneWa !== null);
                const passReviews = reviewCount >= minReviews;
                const passRating = minRating <= 0 || (placeData.rating && placeData.rating >= minRating);

                if (passDistrict && passWebsite && passPhone && passReviews && passRating) {
                    qualifiedCount++;
                    emit('lead', {
                        taskId,
                        name: cleanText(placeData.name),
                        category: cleanText(placeData.category || keyword),
                        phone_raw: cleanText(placeData.phone),
                        phone_wa: phoneWa,
                        address: cleanAddress,
                        review_count: reviewCount,
                        rating: placeData.rating,
                        maps_url: currentUrl,
                        has_website: hasOwnWebsite,
                        website_url: placeData.websiteUrl || null,
                        location_tag: location
                    });
                } else {
                    let reasons = [];
                    if (!passDistrict) reasons.push(`Di luar kecamatan ${targetDistrict}`);
                    if (!passWebsite) reasons.push('Sudah punya website');
                    if (!passPhone) reasons.push('Tidak ada no. telepon');
                    if (!passReviews) reasons.push(`Ulasan < ${minReviews}`);
                    if (!passRating) reasons.push(`Rating < ${minRating}`);

                    emit('skip', {
                        name: placeData.name,
                        reason: reasons.join(', ') || 'Tidak lolos filter',
                        reviewCount,
                        hasOwnWebsite,
                        hasPhone: !!phoneWa
                    });
                }

            } catch (err) {
                emit('warning', { message: `Gagal ekstraksi ${currentUrl}: ${err.message}` });
            }

            // SAFETY DELAY SESUAI PERMINTAAN: Minimal 5 detik, Maksimal 7 detik
            await randomDelay(5000, 7000);
        }

        emit('complete', {
            message: `Scraping selesai! Ditemukan ${qualifiedCount} UMKM potensial (tanpa web, ada WA, ulasan > 5).`,
            totalQualified: qualifiedCount
        });

    } catch (error) {
        emit('error', { message: `Terjadi kesalahan fatal: ${error.message}` });
        process.exitCode = 1;
    } finally {
        if (browser) {
            await browser.close().catch(() => {});
        }
        try {
            fs.rmSync(runnerDir, { recursive: true, force: true });
        } catch (e) {}
    }
}

run();
