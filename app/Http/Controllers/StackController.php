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
    $stack = new Stack();
    $stack->name = $request->input('label');
    //$stack->order = $project->stacks()->max('order') + 1;
    $stack->order = 0;
    $project->stacks()->save($stack);

    return redirect($project->uri);
  }

  public function show(Stack $stack)
  {
    return view('stack.show')
      ->with('stack', $stack)
      ->with('project', $stack->project)
    ;
  }

  public function delete(Stack $stack)
  {
    $stackId = $stack->id;

    $stack->cards->each(function($card) use ($stackId) {
      $newStack = $card->stack->project->stacks->where('id', '!=', $stackId)->first();
      $newStack->touch();

      $card->stack = $newStack;
      $card->save();
    });

    $stack->delete();

    return redirect($stack->project->uri);
  }

}
