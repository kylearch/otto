<?php

namespace App\Models;

class Media extends \Spatie\MediaLibrary\Models\Media
{
    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function showInBrowser()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type'        => $this->mime_type,
            'Content-Length'      => $this->size,
            'Content-Length'      => $this->size,
            'Content-Disposition' => 'inline; filename="' . $this->model->name . '"',
            'Pragma'              => 'public',
        ];

        return response()->stream(function () {
            $stream = $this->stream();
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }

}
