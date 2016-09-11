<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Tag;
use App\Project;

class TagController extends Controller
{

  public function store(Request $request, Project $project) {
    $tag = new Tag();
    $tag->name = $request->input('label');
    $tag->project_id = $project->id;
    $tag->save();

    return redirect($project->uri.'/tags');
  }

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

    $tag->cards->each(function ($card) {
      $card->touch();
    });

    return redirect($tag->uri);
  }

  public function delete(Tag $tag)
  {
    $tag->cards->each(function($card) {
      $card->touch();
      $card->stack->touch();
      $card->tags->each(function ($tag) {
        $tag->touch();
      });
    });
    $tag->delete();

    return redirect($tag->project->uri.'/tags');
  }

}
