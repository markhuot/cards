<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Project;

class ProjectController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    return view('project.index')
      ->with('projects', Project::all())
    ;
  }

  public function store(Request $request)
  {
    $project = new Project($request->input('project'));
    $project->save();

    $project->users()->attach($request->user());

    return redirect($project->uri);
  }

  public function show(Project $project)
  {
    return view('project.show')
      ->with('project', $project)
    ;
  }

}
