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

    $card->assignee_id = $request->input('card.assignee_id', []);
    $card->attachments = $request->file('card.attachment', []);

    $request->user()->follow($card);

    return redirect($project->uri);
  }

  public function show(Request $request, Card $card)
  {
    return view('card.show')
      ->with('q', '#'.$card->local_id)
      ->with('card', $card)
      ->with('project', $card->stack->project)
      ->with('me', $request->user())
    ;
  }

  public function move(Request $request, Card $card)
  {
    if ($stackId = $request->input('card.to.stack_id')) {
      $card->stack_id = $request->input('card.to.stack_id');
      $card->timestamps = false;
      $card->save();
    }

    $targetTagId = $request->input('card.to.tag_id');
    $sourceTagId = $request->input('card.from.tag_id');
    if ($targetTagId && $targetTagId !== $sourceTagId) {
      if ($card->tags->contains($sourceTagId)) {
        $card->tags()->detach($sourceTagId);
      }
      if (!$card->tags->contains($targetTagId)) {
        $card->tags()->attach($targetTagId);
      }
      $card->load('tags');
      $card->saveToSearchIndex();
    }

    $targetAssigneeId = $request->input('card.to.assignee_id');
    $sourceAssigneeId = $request->input('card.from.assignee_id');
    if ($targetAssigneeId && $targetAssigneeId !== $sourceAssigneeId) {
      if ($card->assignees->contains($sourceAssigneeId)) {
        $card->assignees()->detach($sourceAssigneeId);
      }
      if (!$card->assignees->contains($targetAssigneeId)) {
        $card->assignees()->attach($targetAssigneeId);
      }
      $card->load('assignees');
      $card->saveToSearchIndex();
    }

    foreach ($request->input('stack') as $req) {
      $card = Card::find($req['card_id']);
      $card->order = $req['order'];
      $card->save(['touch' => false]);
    }

    return ['ok'];
  }

  public function check(Request $request, Card $card)
  {
    $lineNo = $request->input('line') - 1;
    $value = $request->input('value') ? 'x' : ' ';

    $lines = preg_split('/(\r\n|\r|\n)/', $card->description);
    $line = $lines[$lineNo];

    if (preg_match('/^- \[(x| )\]/', $line)) {
      $lines[$lineNo] = substr_replace($line, $value, 3, 1);
    }

    $card->description = implode("\r\n", $lines);
    $card->save();
  }

  public function unfollow(Request $request, Card $card)
  {
    $request->user()->unfollow($card);

    return redirect($card->uri);
  }

  public function follow(Request $request, Card $card)
  {
    $request->user()->follow($card);

    return redirect($card->uri);
  }

}
