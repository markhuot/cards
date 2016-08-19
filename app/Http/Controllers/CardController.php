<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Project;
use App\Stack;
use App\Card;

class CardController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function create(Project $project, Stack $stack)
  {
    return view('card.create')
      ->with('project', $project)
      ->with('stack', $stack)
    ;
  }

  public function store(Request $request, Project $project, Stack $stack)
  {
    $card = new Card($request->input('card'));
    $stack->cards()->save($card);

    return redirect($project->uri);
  }

  public function show(Card $card)
  {
    return view('card.show')
      ->with('card', $card)
      ->with('project', $card->stack->project)
    ;
  }

}
