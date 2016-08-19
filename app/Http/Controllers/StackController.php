<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Stack;
use App\Project;

class StackController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function store(Project $project, Request $request)
  {
    $stack = new Stack($request->input('stack'));
    $stack->order = $project->stacks()->max('order') + 1;
    $project->stacks()->save($stack);

    return redirect($project->uri);
  }

}
