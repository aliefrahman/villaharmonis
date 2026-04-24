<?php

function compressImage($source, $destination, $quality) {
    $info = @getimagesize($source);

    if ($info === false) return false;

    if ($info['mime'] == 'image/jpeg') 
        $image = @imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif') 
        $image = @imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png') 
        $image = @imagecreatefrompng($source);
    elseif ($info['mime'] == 'image/webp')
        $image = @imagecreatefromwebp($source);
    else
        return false;

    if (!$image) return false;

    // Fix orientation for JPEG
    if ($info['mime'] == 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($source);
        if ($exif && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
            if ($orientation != 1) {
                $deg = 0;
                switch ($orientation) {
                    case 3: $deg = 180; break;
                    case 6: $deg = 270; break;
                    case 8: $deg = 90; break;
                }
                if ($deg) {
                    $image = imagerotate($image, $deg, 0);
                }
            }
        }
    }

    // Pastikan gambar dikonversi dengan baik (membantu jika gambar asal adalah PNG/GIF)
    imagepalettetotruecolor($image);
    imagealphablending($image, true);
    imagesavealpha($image, true);

    // Save image as WebP
    imagewebp($image, $destination, $quality);
    unset($image); // PHP 8.5+ compatible memory cleanup
    
    return true;
}

/**
 * Handle image upload for news
 * @param array $file $_FILES['image']
 * @param string|null $old_image_path Optional old image to delete
 * @return string|false Returns filename on success, false on failure
 */
function handleNewsImageUpload($file, $old_image_path = null) {
    if(!isset($file) || $file['error'] != 0) {
        return false;
    }
    
    $upload_dir = __DIR__ . '/../uploads/news/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = uniqid() . '_' . time() . '.webp';
    $destination = $upload_dir . $filename;
    
    if(compressImage($file['tmp_name'], $destination, 75)) {
        // Delete old image if exists
        if($old_image_path && file_exists($upload_dir . $old_image_path)) {
            @unlink($upload_dir . $old_image_path);
        }
        return $filename;
    }
    return false;
}

/**
 * Delete a news image
 */
function deleteNewsImage($filename) {
    if(!$filename) return;
    $path = __DIR__ . '/../uploads/news/' . $filename;
    if(file_exists($path)) {
        @unlink($path);
    }
}
?>
