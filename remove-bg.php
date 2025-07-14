<?php
function removeBackground(string $imagePath, string $processedImagePath): bool {
    $imageType = exif_imagetype($imagePath);
    $image = null;

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($imagePath);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    $width = imagesx($image);
    $height = imagesy($image);

    // Create a new true color image with transparency
    $processedImage = imagecreatetruecolor($width, $height);
    imagesavealpha($processedImage, true);
    $transparentColor = imagecolorallocatealpha($processedImage, 0, 0, 0, 127);
    imagefill($processedImage, 0, 0, $transparentColor);

    // Get the color of the top-left pixel as the background color
    $backgroundColor = imagecolorat($image, 0, 0);

    // Define a tolerance for color matching
    $tolerance = 30;

    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $currentColor = imagecolorat($image, $x, $y);
            $r = ($currentColor >> 16) & 0xFF;
            $g = ($currentColor >> 8) & 0xFF;
            $b = $currentColor & 0xFF;

            $bg_r = ($backgroundColor >> 16) & 0xFF;
            $bg_g = ($backgroundColor >> 8) & 0xFF;
            $bg_b = $backgroundColor & 0xFF;

            // Simple color distance check
            $colorDistance = abs($r - $bg_r) + abs($g - $bg_g) + abs($b - $bg_b);

            if ($colorDistance > $tolerance) {
                // Not the background color, so copy the pixel
                imagesetpixel($processedImage, $x, $y, $currentColor);
            }
        }
    }

    // Save the processed image as a PNG
    $success = imagepng($processedImage, $processedImagePath);

    // Free up memory
    imagedestroy($image);
    imagedestroy($processedImage);

    return $success;
}
?>
