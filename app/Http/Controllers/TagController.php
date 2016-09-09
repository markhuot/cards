<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Tag;

class TagController extends Controller
{

  public function show(Tag $tag)
  {
    return view('tag.show')
      ->with('tag', $tag)
      ->with('project', $tag->project)
    ;
  }

  public function update(Request $request, Tag $tag)
  {
    // @TODO need to sanitize this input so that it's a hex value
    if ($request->input('tag.color') !== null) {
      $tag->color = $request->input('tag.color');
    }

    $tag->save();

    return redirect($tag->uri);
  }

}
