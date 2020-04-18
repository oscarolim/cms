<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
Use Image;
Use App\File;

class FileController extends Controller
{
    public function index()
    {
        
    }
 
    public function store(Request $request)
    {
        if($request->type == 'image')
            return $this->uploadImage($request);
    }

    private function uploadImage(Request $request)
    {
        $savePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().'/public/uploads/images/';

        request()->validate([
            $request->field_id => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if ($file = $request->file($request->field_id)) 
        {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $new_name = time().'_'.Str::slug($name).'.'.$file->extension();
            $ImageUpload = Image::make($file);
            $ImageUpload->resize(1024, null, function ($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $ImageUpload->save($savePath.$new_name);
        
            $photo = new File();
            $photo->folder = Storage::url('uploads/images/');
            $photo->name = $name;
            $photo->filename = $new_name;
            $photo->type = 'image';
            $photo->width = $ImageUpload->width();
            $photo->height = $ImageUpload->height();
            $photo->size = $ImageUpload->filesize();
            $photo->save();

            return Response()->json($photo);
        }
        
        //$image = File::latest()->first();
    }
}
?>