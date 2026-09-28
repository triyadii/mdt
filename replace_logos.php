<?php
$file = '/var/www/html/WebsiteMDT/resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$logoUrl = 'https://lh3.googleusercontent.com/aida/AEtjO1X5aX4ekhR87npjmAFgU7uT2f6Vm-pcyva2H_pltl9w9GdsErn2-XI4QSu6R4FW7RYsZ_570gRX69jOG-hnajlIEnrDPOJdVxwBj5eZDNc4uKpeWZvWpL4IR63GsJ9OeCq3sGdzYL7dbK7biZ5AjKMCoFccbEtX9xW4GrO4193pSrqlrfVKoQ2sxyPNzhipFumRMEFMxUvbli_3oYvZ8zu-HE5z7bI57fBwGYWiK5aNgmgGSokAfEYL0lKk';

// Replace src="assets/media/logos/logo2.png" with the new logo
$content = str_replace('src="assets/media/logos/logo2.png"', 'src="' . $logoUrl . '"', $content);

file_put_contents($file, $content);
echo "Replaced logos in admin layout\n";
