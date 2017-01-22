<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /*
     * Downloading
     */
    public function getFile($user, $filename)
    {
        $file = $user.'/'.$filename;
        if (!Storage::exists($file)) abort(404);
        return response()->file(Storage::getDriver()->getAdapter()->getPathPrefix() . $file);
    }

    /*
     * Uploading
     */
    public function postUpload(Request $request)
    {
        if (!$request->file('file') || !$request->file('file')->isValid())
            abort(500, 'File not passed to request or upload failed');

        //This is a public non user upload
        if (!$request->has('key'))
            return Response()->json($this->upload($request, 'public'));

    }

    public function upload($request, $directory)
    {
        //Checking we don't overwrite a file (unique file names)
        $filename = null;
        while (true) {
            $filename = sprintf("%s/%s.%s",
                $directory,
                str_random(6),
                $request->file->extension()
            );
            if (!Storage::exists($filename)) break;
        };

        $request->file->storeAs('.', $filename);

        $deletionKey = str_random(64);
        \Cache::forever('delete:' . $deletionKey, $filename);

        return [
            'href'   => \URL::to($filename),
            'delete' => \URL::to('d/' . $deletionKey)
        ];
    }
}
