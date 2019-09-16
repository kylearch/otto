<?php

namespace App\Jobs;

use Alimranahmed\LaraOCR\Facades\OCR;
use App\Models\Storage\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Document
     */
    private $document;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Storage\Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $media = $this->document->getFirstMedia('file');

        $image = $media->getPath();
        if ($media->getTypeFromMime() !== 'image' && $media->hasGeneratedConversion('pdf-png')) {
            $image = $this->document->getFirstMediaPath('file', 'pdf-png');
        }

        $this->document->update([
            'text' => OCR::scan($image),
        ]);
    }

}
