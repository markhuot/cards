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
    $card->stack = $stack;
    $card->user = $request->user();
    $card->order = $stack->cards()->max('order') + 1;
    $card->save();

    $card->assignee_id = $request->input('card.assignee_id');

    $request->user()->follow($card);

    return redirect($project->uri);
  }

  public function show(Card $card)
  {
    return view('card.show')
      ->with('card', $card)
      ->with('project', $card->stack->project)
    ;
  }

  public function move(Request $request, Card $card)
  {
    $card->stack_id = $request->input('card.stack_id');
    $card->timestamps = false;
    $card->save();

    foreach ($request->input('stack') as $req) {
      $card = Card::find($req['card_id']);
      $card->timestamps = false;
      $card->order = $req['order'];
      $card->save();
    }

    return ['ok'];
  }

}
