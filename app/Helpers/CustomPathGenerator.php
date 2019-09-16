<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return string
     */
    public function getPath(Media $media): string
    {
        return Str::plural(class_basename($media->model)) . '/' . $media->id . '/';
    }

    /**
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'c/';
    }

    /**
     * @param \Spatie\MediaLibrary\Models\Media $media
     *
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'cri/';
    }

}
