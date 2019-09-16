<?php

namespace App\Http\Controllers;

use App\Models\Storage\Document;
use Exception;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documents = Document::orderByDesc('id')->paginate(30);

        return view('pages.documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.documents.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $document = Document::firstOrNew([
                'hash' => hash_file('md5', $request->file('document')->getPath()),
            ]);

            $document->name = $request->file('document')->getClientOriginalName();
            $document->save();

            $document->addMediaFromRequest('document')->toMediaCollection('file');
        } catch (Exception $e) {
            return redirect()->back()->withException($e);
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Storage\Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        return $document->getFirstMedia('file')->showInBrowser();
    }

    /**
     * Download the specified resource.
     *
     * @param \Illuminate\Http\Request     $request
     * @param \App\Models\Storage\Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, Document $document)
    {
        return $document->getFirstMedia('file')->toResponse($request);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
