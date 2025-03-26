<?php

namespace App\Http\Traits;

use Intervention\Image\Facades\Image;

trait Upload
{
    public function makeDirectory($path)
    {
        if (file_exists($path)) return true;
        return mkdir($path, 0755, true);
    }

    public function removeFile($path)
    {
        return file_exists($path) && is_file($path) ? @unlink($path) : false;
    }

    public function uploadImage($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
    {
        // Ensure directory exists
        if (!file_exists($location)) {
            mkdir($location, 0777, true);
        }

        // Remove old files if needed
        if (!empty($old)) {
            if (file_exists($location . '/' . $old)) {
                unlink($location . '/' . $old);
            }
            if (file_exists($location . '/thumb_' . $old)) {
                unlink($location . '/thumb_' . $old);
            }
        }

        // Generate a unique filename if not provided
        if ($filename === null) {
            $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
        }

        // Move the uploaded file to the destination folder
        $file->move($location, $filename);

        // If a thumbnail is required, create a simple resized image
        if (!empty($thumb)) {
            list($thumbWidth, $thumbHeight) = explode('x', $thumb);

            // Resize the image using PHP's GD library
            $this->resizeImage($location . '/' . $filename, $location . '/thumb_' . $filename, (int)$thumbWidth, (int)$thumbHeight);
        }

        return $filename;
    }

    /**
     * Resize an image using PHP's GD Library
     */
    private function resizeImage($sourcePath, $destinationPath, $newWidth, $newHeight)
    {
        list($width, $height, $type) = getimagesize($sourcePath);

        // Create a new blank image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Load the image based on its type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false; // Unsupported format
        }

        // Resize and save the image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $destinationPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $destinationPath);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $destinationPath);
                break;
        }

        // Free up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
    }



}

