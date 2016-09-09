<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Project;
use Gate;

class ProjectController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
    return view('project.index')
      ->with('projects', $request->user()->projects)
    ;
  }

  public function store(Request $request)
  {
    $project = new Project($request->input('project'));
    $project->save();

    $project->users()->attach($request->user());

    return redirect($project->uri);
  }

  public function show(Request $request, Project $project)
  {
    if ($request->user()->cannot('see', $project)) {
      abort(404);
    }

    $q = $request->input('q');
    $cardId = false;
    if (substr($q, 0, 1) == '#') {
      $cardId = substr($q, 1);
    }
    else if (is_numeric($q)) {
      $cardId = $q;
    }

    if ($cardId) {
      if ($card=$project->cards()->where('local_id', '=', $cardId)->first()) {
        return redirect('cards/'.$card->id);
      }
    }

    // This is a little risky, but I know that only routes for stacks, tags, users
    // and milestones point here so I'm not too concerned about someone
    // throwing some bogus segment here that gets calleda against
    // $project-> further down.
    $method = $request->segment(3, 'stacks');

    return view('project.show')
      ->with('q', $request->q)
      ->with('project', $project)
      ->with('stacks', $project->{$method})
      ->with('method', $method)
      ->with('searchUri', '/'.$request->path())
    ;
  }

}
