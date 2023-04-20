<?php

namespace App\Service;

class ImageValidator
{

    public function containsImage($content)
    {
        $imageExtensions = ['jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'png', 'PNG'];

        foreach ($imageExtensions as $extension) {
            if (strpos($content, '.' . $extension) !== false) {
                return true;
            }
        }

        return false;
    }


    public function isValidImageUrl($url)
    {
        return (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) !== false);
    }
}