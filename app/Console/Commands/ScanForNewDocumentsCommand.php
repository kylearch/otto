<?php

namespace App\Console\Commands;

use App\Models\Storage\Document;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class ScanForNewDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans tmp directory for unsaved documents';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $directory = storage_path('app/public/tmp');
        $files     = File::allFiles($directory);

        foreach ($files as $file) {
            $document = Document::firstOrNew([
                'hash' => hash_file('md5', $file->getRealPath()),
            ]);

            $document->name = $file->getFilename();
            $document->save();

            $document->addMedia($file->getRealPath())->toMediaCollection('file');
        }

        $fileSystem = new Filesystem();
        $fileSystem->cleanDirectory($directory);
    }
}
