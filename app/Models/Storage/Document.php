<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Tags\HasTags;

class Document extends Model implements HasMedia
{
    use SoftDeletes, HasTags, HasMediaTrait;

    const CONVERSION_THUMB   = 'thumb';
    const CONVERSION_PDF_PNG = 'pdf-png';

    /**
     * @var array
     */
    protected $fillable = ['name', 'text', 'hash'];

    /**
     * @return void
     */
    public function registerMediaCollections()
    {
        $this->addMediaCollection('file')->singleFile()->registerMediaConversions(function (Media $media) {
            $this->addMediaConversion(self::CONVERSION_THUMB)->width(256)->queued();
            if ($media->getTypeFromMime() === 'pdf') {
                $this->addMediaConversion(self::CONVERSION_PDF_PNG)->format('png')->queued();
            }
        });
    }

}
