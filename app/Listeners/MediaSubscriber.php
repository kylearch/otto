<?php

namespace App\Listeners;

use Alimranahmed\LaraOCR\Facades\OCR;
use App\Models\Storage\Document;
use Spatie\MediaLibrary\Events\ConversionHasBeenCompleted;
use Spatie\MediaLibrary\Events\MediaHasBeenAdded;

class MediaSubscriber
{
    /**
     * Handle user login events.
     *
     * @param \Spatie\MediaLibrary\Events\MediaHasBeenAdded $event
     */
    public function mediaHasBeenAdded(MediaHasBeenAdded $event)
    {
        if (!is_a($event->media->model, Document::class)) {
            return;
        }

        if ($event->media->getTypeFromMime() === 'image') {
            $this->updateDocumentText($event);
        }
    }

    /**
     * Handle user logout events.
     *
     * @param \Spatie\MediaLibrary\Events\ConversionHasBeenCompleted $event
     */
    public function conversionHasBeenCompleted(ConversionHasBeenCompleted $event)
    {
        if (!is_a($event->media->model, Document::class)) {
            return;
        }

        if ($event->conversion->getName() === Document::CONVERSION_PDF_PNG) {
            $this->updateDocumentText($event);
        }
    }

    /**
     * @param MediaHasBeenAdded|ConversionHasBeenCompleted $event
     */
    private function updateDocumentText($event)
    {
        $conversion = optional($event->conversion)->getName() ?: null;
        $image      = $event->media->getPath($conversion);

        /** @var Document $document */
        $document = $event->media->model;
        $document->update([
            'text' => OCR::scan($image),
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(MediaHasBeenAdded::class, 'App\Listeners\MediaSubscriber@mediaHasBeenAdded');
        $events->listen(ConversionHasBeenCompleted::class, 'App\Listeners\MediaSubscriber@conversionHasBeenCompleted');
    }
}
