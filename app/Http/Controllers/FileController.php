<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

use App\User;
use App\Files;



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

    public function getThumb($user, $filename)
    {
        $file = $user.'/thumbs/'.$filename;
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
        if (!$request->has('key')) {
            return Response()->json($this->upload($request, 'public'));
        }else {
            //This is a user upload
            return Response()->json($this->upload($request, User::where('key', '=', $request->key)->pluck('directory')->first()));
        }
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

        //create thumbnail of image
        $fileInfo = pathinfo(storage_path($filename));
        $filename_noextension = array_get($fileInfo, 'filename');
        $extension = array_get($fileInfo, 'extension');
        $thumbname = 'thumb-'.$filename_noextension.'.'.$extension;
        $thumb = Image::make(Storage::get($filename))->resize(100,100);
        $thumb->save(storage_path('/uploads/'.$directory.'/thumbs/'.$thumbname));

        if (!$request->has('key')) {
            $id = null;
        }else {
            $id = User::where('key', '=', $request->key)->pluck('id')->first();
        }

        DB::table('files')->insert([
                'fullurl' => \URL::to($filename),
                'imagepath' => $filename,
                'thumbpath' => $directory.'/thumbs/'.$thumbname,
                'deletionkey' => $deletionKey,
                'id' => $id,
            ]);



        return [
            'href'   => \URL::to($filename),
        ];
    }

    /*
     * Deleting image from storage as well as from the database
     */
    public function deleteImage()
        {

            $query = Files::where('deletionkey', '=', $_GET['deletionkey'])->get();
            foreach ($query as $details)
            {
                $delete_image_path = $details->imagepath;
                $delete_thumb_path = $details->thumbpath;
            }
            DB::table('files')->where('deletionkey', '=', $_GET['deletionkey'])->delete();
            Storage::delete([$delete_image_path, $delete_thumb_path]);

            return redirect('home');
        }


}
