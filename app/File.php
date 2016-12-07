<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Trace;
use App\Traits\FilesTrait;

class File extends Model
{
    use FilesTrait;

    protected $fillable = ['basename', 'dirname', 'extension', 'originename', 'type', 'tmp_name', 'error', 'size', 'local_name', 'local_path', 'status'];

    public function campaignplannings()
    {
        return $this->hasMany('App\Campaignplannings');
    }

    public function campaigns()
    {
        return $this->hasMany('App\Campaigns');
    }

    public function storeAndSave(&$trace, $file)
    {
        /*if ($file !== UPLOAD_ERR_OK) {
            return -1;
        }*/

        if (!is_null($trace)){
            $trace->startnew("enregistrement du fichier " . $file["name"]);
        }

        $traceresult = ['execode' => 1, 'exestring' => "succès", 'result' => 1];

        // ensure a safe filename
        $originename = preg_replace("/[^A-Z0-9._-]/i", "_", $file["name"]);

        $parts = pathinfo($originename);
        $new_extension = $parts['extension'];//"csv";//

        $local_name = uniqid();
        $local_path = public_path('files/receivers/') . $local_name . '.' . $new_extension;

        // upload image to local server
        $move_rslt = move_uploaded_file($file['tmp_name'], $local_path);

        if ($move_rslt) {
            //
        } else {
            $this->copyFile($file['tmp_name'], $local_path);
        }

        // make image entry to DB
        $filedb = File::create([
            'basename' => $parts['basename'],
            'dirname' => $parts['dirname'],
            'extension' => $parts['extension'],
            'originename' => $parts['filename'],
            'type' => $file['type'],
            'tmp_name' => $file['tmp_name'],
            'error' => $file['error'],
            'size' => $file['size'],
            'local_name' => $local_name,
            'local_path' => $local_path,
            'status' => false,
        ]);

        if (!is_null($trace)){
            $trace->endone($traceresult['execode'], $traceresult['exestring'], $traceresult['result']);
        }

        return [1, $filedb];
    }

    public function uploadAndSave($file, $extension, $mimeType, $fileSize)//Request $request)
    {
        // get basic info
        //$s3 = Storage::disk('s3');
        //$file = $request->file('file');
//        $extension = $request->file('file')->guessExtension();
        $filename = uniqid() . '.' . $extension;
//        $mimeType = $request->file('file')->getClientMimeType();
//        $fileSize = $request->file('file')->getClientSize();
        //$image = Image::make($file);
        //$galleryId = $request->input('galleryId');

        // generate the thumb and medium image
        //$imageThumb = Image::make($file)->fit(320)->crop(320, 240, 0, 0);
        //$imageThumb->encode($extension);

        /*$imageMedium = Image::make($file)->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $imageMedium->encode($extension);*/

        //$image->encode($extension);

        // upload image to S3
        /*$s3->put("gallery_{$galleryId}/main/" . $filename, (string) $image, 'public');
        $s3->put("gallery_{$galleryId}/medium/" . $filename, (string) $imageMedium, 'public');
        $s3->put("gallery_{$galleryId}/thumb/" . $filename, (string) $imageThumb, 'public');*/

        // upload image to local server
        $file->move(public_path('files/receivers/'));

        // make image entry to DB
        $file = File::create([
            'file_name' => $filename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_path' => "files/receivers/" . $filename,
            'type' => 'receivers',
        ]);

        /*DB::table('gallery_images')->insert([
            'gallery_id' => $galleryId,
            'file_id' => $file->id,
        ]);*/

        $fileImg = File::find($file->id);
        $fileImg->status = 1;
        $fileImg->save();

        return [
            'file' => $fileImg,
            'file_id' => $file->id,
            'path' => "files/receivers/" . $filename,
        ];
    }

    private function normalizeFilesArray($files = [])
    {
        $normalized_array = [];

        foreach ($files as $index => $file) {
            if (!is_array($file['name'])) {
                $normalized_array[$index][] = $file;
                continue;
            }

            foreach ($file['name'] as $idx => $name) {
                $normalized_array[$index][$idx] = [
                    'name' => $name,
                    'type' => $file['type'][$idx],
                    'tmp_name' => $file['tmp_name'][$idx],
                    'error' => $file['error'][$idx],
                    'size' => $file['size'][$idx]
                ];
            }
        }

        return $normalized_array;

    }
}
