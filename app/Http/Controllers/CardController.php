<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Project;
use App\Stack;
use App\Card;
use App\Tag;
use App\User;

class CardController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function create(Request $request, $sourceId)
  {
    $card = new Card;

    switch($request->segment(1)) {
      case 'stacks':
        $source = Stack::find($sourceId);
        $card->stack_id = $sourceId;
        $backButtonUrl = $source->project->uri;
        break;
      case 'tags':
        $source = Tag::find($sourceId);
        $card->stack_id = $source->project->stacks->first()->getKey();
        $card->tags->push($source);
        $backButtonUrl = $source->project->uri.'/tags';
        break;
      case 'users':
        $source = User::find($sourceId);
        $card->stack_id = 1;
        $backButtonUrl = '/tags';
        break;
    }

    return view('card.create')
      ->with('source', $source)
      ->with('project', $source->project)
      ->with('card', $card)
      ->with('showBackButton', true)
      ->with('backButtonUrl', $backButtonUrl)
    ;
  }

  public function store(Request $request)
  {
    $card = new Card($request->input('card'));
    $card->stack_id = $request->input('card.stack_id');
    $card->user = $request->user();
    $card->order = $card->stack->cards()->groupBy('cards.id')->max('order') + 1;
    $card->save();

    $card->setTagsByString($request->input('card.tags'));
    $card->setAssigneesById($request->input('card.assignee_id', []));

    $card->attachments = $request->file('card.attachment', []);

    $request->user()->follow($card);

    $card->stack->touch();
    $card->tags->each(function ($tag) {
      $tag->touch();
    });

    return redirect($card->uri);
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
      // touch the existing stack, to notate the removal
      $card->stack->touch();

      // Update the stack
      $card->stack_id = $request->input('card.to.stack_id');
      $card->timestamps = false;
      $card->save();

      // load the new stack, so we have a proper reference
      $card->load('stack');
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
      $card->touch();
      $card->load('tags');
      $card->saveToSearchIndex();

      Tag::find($sourceTagId)->touch();
      Tag::find($targetTagId)->touch();
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
      $card->touch();
      $card->load('assignees');
      $card->saveToSearchIndex();

      User::find($sourceAssigneeId)->touch();
      $card->assignees->each(function ($user) {
        $user->touch();
      });
    }

    foreach ($request->input('stack') as $req) {
      $sibling = Card::find($req['card_id']);
      $sibling->order = $req['order'];
      $sibling->timestamps = false;
      $sibling->save();
    }

    $card->stack->touch();
    $card->tags->each(function($tag) {
      $tag->touch();
    });
    $card->assignees->each(function($assignee) {
      $assignee->touch();
    });

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
