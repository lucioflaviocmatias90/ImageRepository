<?php

/*
 * Adicione essa configuração na pasta /config/filesystems.php
 *
 * Obs: essa configuração é opcional caso queria salvar nas pasta /public
 *
 *
 * 'public_uploads' => [
        'driver' => 'local',
        'root'   => public_path() . '/uploads',
        'url' => env('APP_URL').'/uploads',
        'visibility' => 'public',
    ],
*/

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
    private $imageFields;
    private $folderDestination;
    private $storageDisk;
    private $newWidth;
    private $path;

    public function __construct(array $imageFields, $folderDestination = 'photos', $storageDisk = 'public', int $newWidth = 500)
    {
        $this->imageFields = $imageFields;
        $this->folderDestination = $folderDestination;
        $this->storageDisk = $storageDisk;
        $this->newWidth = $newWidth;
    }

    public function uploadImage($request, $objModel)
    {        
        foreach ($this->imageFields as $imageField) {
            // arquivo binário
            if ($request->hasFile($imageField)) {
                $this->path = collect($request->file($imageField))->map(function($image) {
                    return $this->resizeImage($image);
                });
            }

            // arquivo base64
            if (!is_null($request->input($imageField)) && is_array($request->input($imageField))) {
                $this->path = collect($request->input($imageField))->map(function($base64String){
                    return $this->resizeImage(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64String)));
                });
            }

            $objModel->update([ $imageField => $this->path ]);
        }
    }

    public function resizeImage($image_to_resize)
    {
        $width = Image::make($image_to_resize)->width();

        if ($width > $this->newWidth) {
            $resized_image = Image::make($image_to_resize)->resize($this->newWidth, null, function($constraint) {
                $constraint->aspectRatio();
            })->stream('jpg', 75);
        } else {
            $resized_image = Image::make($image_to_resize)->stream('jpg', 75);
        }

        $imageName = Str::random(10) . '.jpg';

        Storage::disk($this->storageDisk)->put($this->folderDestination.'/'.$imageName, $resized_image);

        switch ($this->storageDisk) {
            case 'public_uploads':
                return '/uploads/'.$this->folderDestination.'/'.$imageName;
                break;

            case 'public':
                return '/storage/'.$this->folderDestination.'/'.$imageName;
                break;
        }
    }

    public function verifyImageInStorage($objModel)
    {
        collect($this->imageFields)->each(function($imageField) use ($objModel) {
            if (!is_null($objModel->$imageField) && is_array($objModel->$imageField)) {
                collect($objModel->$imageField)->each(function($photo) {
                    switch ($this->storageDisk) {
                        case 'public_uploads':
                            Storage::disk($this->storageDisk)->delete(str_replace('/uploads/', '', $photo));
                            break;
                        case 'public':
                            Storage::disk($this->storageDisk)->delete(str_replace('/storage/', '', $photo));
                            break;
                    }
                });
            }
        });
    }
}