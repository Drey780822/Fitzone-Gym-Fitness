<?php
session_start();

// Check if GD library is available
if (!extension_loaded('gd')) {
    die('GD library is not enabled on this server.');
}

// Generate random CAPTCHA code
$captcha_code = sprintf("%04d", rand(0, 9999)); // Ensure 4 digits
$_SESSION['captcha'] = $captcha_code;

// Create image (120x40 pixels)
$image = imagecreatetruecolor(120, 40);
if (!$image) {
    die('Failed to create image.');
}

// Set colors
$bg_color = imagecolorallocate($image, 0, 0, 0); // Black background
$text_color = imagecolorallocate($image, 255, 255, 255); // White text
$noise_color = imagecolorallocate($image, 100, 100, 100); // Gray for noise

// Fill background
imagefill($image, 0, 0, $bg_color);

// Add noise (random lines and dots)
for ($i = 0; $i < 5; $i++) {
    imageline(
        $image,
        rand(0, 120),
        rand(0, 40),
        rand(0, 120),
        rand(0, 40),
        $noise_color
    );
}
for ($i = 0; $i < 50; $i++) {
    imagesetpixel(
        $image,
        rand(0, 120),
        rand(0, 40),
        $noise_color
    );
}

// Add CAPTCHA text (centered)
$text_width = imagefontwidth(5) * strlen($captcha_code);
$text_height = imagefontheight(5);
$x = (120 - $text_width) / 2; // Center horizontally
$y = (40 - $text_height) / 2; // Center vertically
imagestring($image, 5, $x, $y, $captcha_code, $text_color);

// Set header and output image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>