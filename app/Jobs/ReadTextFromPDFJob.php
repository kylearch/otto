<?php

namespace App\Jobs;

use Alimranahmed\LaraOCR\Facades\OCR;
use App\Models\Storage\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReadTextFromPDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Storage\Document
     */
    private $document;

    /**
     * @var string
     */
    private $imagePath;

    /**
     * Create a new job instance.
     *
     * @param Document $document
     *
     * @return void
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
        $this->convertToTiff();
        $this->readText();
    }

    /**
     * @return void
     */
    private function convertToTiff()
    {
        $input           = trim(escapeshellarg($this->document->getFirstMediaPath('file')));
        $this->imagePath = preg_replace('/\.pdf/', '.tiff', $input);

        exec("convert -density 300 {$input} -depth 8 {$this->imagePath} 2>&1 &");
    }

    /**
     * @return void
     */
    private function readText()
    {
        if (!file_exists($this->imagePath)) {
            sleep(1);
            $this->readText();
        } else {
            dd("Done");
        }

        // $this->document->update(['text' => OCR::scan($this->imagePath)]);
    }
}
