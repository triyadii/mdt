<?php

namespace App\Services;

class IndonesiaRegionService
{
    /**
     * Data Wilayah Indonesia (Provinsi, Kota/Kabupaten, dan Kecamatan)
     */
    protected static array $regions = [
        'Sumatera Utara' => [
            'Kota Medan' => [
                'Medan Selayang', 'Medan Baru', 'Medan Petisah', 'Medan Barat', 'Medan Kota',
                'Medan Sunggal', 'Medan Johor', 'Medan Timur', 'Medan Helvetia', 'Medan Denai',
                'Medan Tembung', 'Medan Amplas', 'Medan Area', 'Medan Perjuangan', 'Medan Polonia',
                'Medan Maimun', 'Medan Tuntungan', 'Medan Deli', 'Medan Labuhan', 'Medan Marelan', 'Medan Belawan',
            ],
            'Kabupaten Deli Serdang' => ['Lubuk Pakam', 'Tanjung Morawa', 'Percut Sei Tuan', 'Sunggal', 'Batang Kuis', 'Pancur Batu', 'Deli Tua'],
            'Kota Binjai' => ['Binjai Kota', 'Binjai Barat', 'Binjai Timur', 'Binjai Utara', 'Binjai Selatan'],
            'Kota Pematangsiantar' => ['Siantar Barat', 'Siantar Timur', 'Siantar Selatan', 'Siantar Utara', 'Siantar Marihat'],
            'Kabupaten Karo' => ['Kabanjahe', 'Berastagi', 'Tigapanah', 'Merek'],
            'Kabupaten Asahan' => ['Kisaran Barat', 'Kisaran Timur', 'Air Joman'],
            'Kabupaten Simalungun' => ['Girsang Sipangan Bolon', 'Raya', 'Siantar', 'Tapian Dolok'],
        ],
        'DKI Jakarta' => [
            'Jakarta Selatan' => ['Tebet', 'Setiabudi', 'Mampang Prapatan', 'Kebayoran Baru', 'Kebayoran Lama', 'Cilandak', 'Pasar Minggu', 'Pancoran', 'Jagakarsa', 'Pesanggrahan'],
            'Jakarta Pusat' => ['Gambir', 'Menteng', 'Tanah Abang', 'Senen', 'Cempaka Putih', 'Kemayoran', 'Sawah Besar', 'Johar Baru'],
            'Jakarta Barat' => ['Grogol Petamburan', 'Kebon Jeruk', 'Palmerah', 'Kembangan', 'Taman Sari', 'Tambora', 'Kalideres', 'Cengkareng'],
            'Jakarta Timur' => ['Matraman', 'Jatinegara', 'Duren Sawit', 'Kramat Jati', 'Pasar Rebo', 'Ciracas', 'Cipayung', 'Cakung', 'Pulo Gadung'],
            'Jakarta Utara' => ['Penjaringan', 'Tanjung Priok', 'Koja', 'Kelapa Gading', 'Pademangan', 'Cilincing'],
        ],
        'Jawa Barat' => [
            'Kota Bandung' => ['Coblong', 'Lengkong', 'Bandung Wetan', 'Sumur Bandung', 'Regol', 'Sukajadi', 'Cidadap', 'Cicendo', 'Andir', 'Antapani', 'Buahbatu'],
            'Kota Bekasi' => ['Bekasi Barat', 'Bekasi Timur', 'Bekasi Selatan', 'Bekasi Utara', 'Pondok Gede', 'Jatiasih', 'Mustika Jaya'],
            'Kota Depok' => ['Pancoran Mas', 'Beji', 'Sukmajaya', 'Cimanggis', 'Cinere', 'Sawangan', 'Tapos'],
            'Kota Bogor' => ['Bogor Tengah', 'Bogor Selatan', 'Bogor Timur', 'Bogor Barat', 'Bogor Utara', 'Tanah Sareal'],
            'Kabupaten Bogor' => ['Cibinong', 'Ciawi', 'Cisarua', 'Babakan Madang', 'Gunung Putri', 'Bojonggede'],
            'Kota Tangerang Selatan' => ['Serpong', 'Serpong Utara', 'Pondok Aren', 'Ciputat', 'Ciputat Timur', 'Pamulang', 'Setu'],
            'Kota Cimahi' => ['Cimahi Tengah', 'Cimahi Utara', 'Cimahi Selatan'],
            'Kota Cirebon' => ['Kejaksan', 'Kesambi', 'Lemahwungkuk', 'Harjamukti', 'Pekalipan'],
        ],
        'Jawa Timur' => [
            'Kota Surabaya' => ['Tegalsari', 'Genteng', 'Gubeng', 'Wonokromo', 'Rungkut', 'Sukolilo', 'Mulyorejo', 'Kenjeran', 'Sawahan', 'Sambikerep'],
            'Kota Malang' => ['Klojen', 'Blimbing', 'Lowokwaru', 'Sukun', 'Kedungkandang'],
            'Kabupaten Sidoarjo' => ['Sidoarjo', 'Waru', 'Candi', 'Gedangan', 'Taman', 'Buduran'],
            'Kabupaten Gresik' => ['Gresik', 'Kebomas', 'Manyar', 'Driyorejo'],
            'Kota Batu' => ['Batu', 'Bumiaji', 'Junrejo'],
            'Kota Kediri' => ['Kediri', 'Mojoroto', 'Pesantren'],
        ],
        'Jawa Tengah' => [
            'Kota Semarang' => ['Semarang Tengah', 'Semarang Barat', 'Semarang Timur', 'Semarang Selatan', 'Semarang Utara', 'Candisari', 'Gajahmungkur', 'Banyumanik', 'Pedurungan'],
            'Kota Surakarta (Solo)' => ['Banjarsari', 'Jebres', 'Laweyan', 'Pasar Kliwon', 'Serengan'],
            'Kota Magelang' => ['Magelang Tengah', 'Magelang Utara', 'Magelang Selatan'],
            'Kota Salatiga' => ['Sidorejo', 'Tingkir', 'Argomulyo', 'Sidomukti'],
            'Kota Tegal' => ['Tegal Barat', 'Tegal Timur', 'Tegal Selatan', 'Margadana'],
            'Kota Pekalongan' => ['Pekalongan Barat', 'Pekalongan Timur', 'Pekalongan Utara', 'Pekalongan Selatan'],
        ],
        'DI Yogyakarta' => [
            'Kota Yogyakarta' => ['Danurejan', 'Gedongtengen', 'Gondokusuman', 'Gondomanan', 'Jetis', 'Kotagede', 'Kraton', 'Mantrijeron', 'Mergangsan', 'Ngampilan', 'Pakualaman', 'Tegalrejo', 'Umbulharjo', 'Wirobrajan'],
            'Kabupaten Sleman' => ['Depok', 'Mlati', 'Ngaglik', 'Gamping', 'Kalasan', 'Sleman'],
            'Kabupaten Bantul' => ['Bantul', 'Banguntapan', 'Sewon', 'Kasihan', 'Piyungan'],
            'Kabupaten Gunungkidul' => ['Wonosari', 'Playen', 'Patuk'],
            'Kabupaten Kulon Progo' => ['Wates', 'Pengasih', 'Temon'],
        ],
        'Bali' => [
            'Kota Denpasar' => ['Denpasar Selatan', 'Denpasar Barat', 'Denpasar Utara', 'Denpasar Timur'],
            'Kabupaten Badung' => ['Kuta', 'Kuta Utara (Canggu/Seminyak)', 'Kuta Selatan (Nusa Dua/Jimbaran)', 'Mengwi', 'Abiansemal'],
            'Kabupaten Gianyar' => ['Ubud', 'Gianyar', 'Sukawati', 'Blahbatuh', 'Tampaksiring'],
            'Kabupaten Tabanan' => ['Tabanan', 'Kediri', 'Baturiti (Bedugul)'],
            'Kabupaten Buleleng' => ['Buleleng (Singaraja)', 'Seririt'],
        ],
        'Banten' => [
            'Kota Tangerang' => ['Tangerang', 'Cipondoh', 'Karawaci', 'Ciledug', 'Pinang', 'Batuceper', 'Jatiuwung'],
            'Kota Cilegon' => ['Cilegon', 'Cibeber', 'Grogol', 'Pulomerak'],
            'Kota Serang' => ['Serang', 'Cipocok Jaya', 'Curug', 'Kasemen', 'Taktakan'],
        ],
        'Riau & Kepulauan Riau' => [
            'Kota Pekanbaru' => ['Marpoyan Damai', 'Payung Sekaki', 'Sukajadi', 'Tuah Madani', 'Bukit Raya', 'Pekanbaru Kota'],
            'Kota Batam' => ['Batam Kota', 'Lubuk Baja', 'Sekupang', 'Nongsa', 'Bengkong', 'Batu Ampar'],
            'Kota Tanjungpinang' => ['Tanjungpinang Kota', 'Tanjungpinang Barat', 'Tanjungpinang Timur', 'Bukit Bestari'],
        ],
        'Sumatera Barat' => [
            'Kota Padang' => ['Padang Barat', 'Padang Timur', 'Padang Selatan', 'Padang Utara', 'Koto Tangah', 'Kuranji'],
            'Kota Bukittinggi' => ['Guguk Panjang', 'Mandiangin Koto Selayan', 'Aur Birugo Tigo Baleh'],
            'Kota Payakumbuh' => ['Payakumbuh Barat', 'Payakumbuh Timur', 'Payakumbuh Utara'],
        ],
        'Sumatera Selatan' => [
            'Kota Palembang' => ['Ilir Barat I', 'Ilir Barat II', 'Ilir Timur I', 'Ilir Timur II', 'Sukarami', 'Plaju', 'Kemuning', 'Kalidoni'],
            'Kota Prabumulih' => ['Prabumulih Timur', 'Prabumulih Barat'],
        ],
        'Lampung' => [
            'Kota Bandar Lampung' => ['Tanjung Karang Pusat', 'Tanjung Karang Timur', 'Kedaton', 'Way Halim', 'Kemiling', 'Teluk Betung Selatan'],
            'Kota Metro' => ['Metro Pusat', 'Metro Barat', 'Metro Timur'],
        ],
        'Kalimantan Timur' => [
            'Kota Samarinda' => ['Samarinda Kota', 'Samarinda Ulu', 'Samarinda Ilir', 'Sungai Pinang', 'Palaran'],
            'Kota Balikpapan' => ['Balikpapan Kota', 'Balikpapan Selatan', 'Balikpapan Tengah', 'Balikpapan Utara', 'Balikpapan Barat'],
            'Kabupaten Penajam Paser Utara (IKN)' => ['Sepaku (IKN Nusantara)', 'Penajam'],
        ],
        'Kalimantan Barat' => [
            'Kota Pontianak' => ['Pontianak Kota', 'Pontianak Selatan', 'Pontianak Tenggara', 'Pontianak Barat', 'Pontianak Utara'],
            'Kota Singkawang' => ['Singkawang Barat', 'Singkawang Tengah'],
        ],
        'Kalimantan Selatan' => [
            'Kota Banjarmasin' => ['Banjarmasin Tengah', 'Banjarmasin Barat', 'Banjarmasin Timur', 'Banjarmasin Selatan', 'Banjarmasin Utara'],
            'Kota Banjarbaru' => ['Banjarbaru Utara', 'Banjarbaru Selatan', 'Landasan Ulin'],
        ],
        'Sulawesi Selatan' => [
            'Kota Makassar' => ['Ujung Pandang', 'Panakkukang', 'Rappocini', 'Tamalanrea', 'Biringkanaya', 'Mariso', 'Mamajang', 'Makassar', 'Bontoala'],
            'Kabupaten Gowa' => ['Somba Opu', 'Pallangga'],
            'Kabupaten Maros' => ['Turikale', 'Mandai'],
        ],
        'Sulawesi Utara' => [
            'Kota Manado' => ['Wenang', 'Sario', 'Malalayang', 'Tikala', 'Mapanget'],
            'Kota Tomohon' => ['Tomohon Tengah', 'Tomohon Selatan', 'Tomohon Utara'],
        ],
        'Nusa Tenggara Barat' => [
            'Kota Mataram' => ['Mataram', 'Ampenan', 'Cakranegara', 'Sekarbela', 'Sandubaya', 'Selaparang'],
            'Kabupaten Lombok Barat' => ['Gerung', 'Batu Layar (Senggigi)', 'Narmada'],
        ],
        'Papua' => [
            'Kota Jayapura' => ['Jayapura Utara', 'Jayapura Selatan', 'Abepura', 'Heram', 'Muara Tami'],
            'Kabupaten Mimika' => ['Kuala Kencana', 'Mimika Baru (Timika)'],
        ],
    ];

    /**
     * Dapatkan daftar seluruh provinsi
     */
    public static function getProvinces(): array
    {
        return array_keys(self::$regions);
    }

    /**
     * Dapatkan daftar kota/kabupaten berdasarkan provinsi
     */
    public static function getCities(string $province): array
    {
        if (! isset(self::$regions[$province])) {
            return [];
        }

        return array_keys(self::$regions[$province]);
    }

    /**
     * Dapatkan daftar kecamatan berdasarkan provinsi & kota
     */
    public static function getDistricts(string $province, string $city): array
    {
        if (! isset(self::$regions[$province][$city])) {
            return [];
        }

        return self::$regions[$province][$city];
    }

    /**
     * Dapatkan semua data dalam bentuk JSON untuk frontend
     */
    public static function getAllRegionsJson(): string
    {
        return json_encode(self::$regions);
    }
}
