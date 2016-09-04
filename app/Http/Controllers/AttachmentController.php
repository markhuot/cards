<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Attachment;
use Intervention\Image\ImageManager;
use Storage;

class AttachmentController extends Controller
{

  public function show(Request $request, ImageManager $manager, Attachment $attachment)
  {
    $image = $manager->make(storage_path('app/'.$attachment->link));
    return $image->response();
  }

  public function thumb(Request $request, ImageManager $manager, Attachment $attachment)
  {
    $size = $request->input('s', 32);

    $image = $manager->make(storage_path('app/'.$attachment->link));
    $image->resize($size, $size);
    return $image->response();
  }

}
