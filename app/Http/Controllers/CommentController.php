<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCardComment;
use App\Attachment;
use App\Comment;
use App\Card;
use App\User;
use App\Stack;
use App\Deltas\CardDelta;

class CommentController extends Controller
{

  public function store(StoreCardComment $request, Card $card)
  {
    $card->complete = $request->input('card.complete');
    $card->stack_id = $request->input('card.stack_id');
    $card->save();

    $card->setTagsByString($request->input('card.tags'));
    $card->setAssigneesById($request->input('card.assignee_id', []));

    $card->load('stack');
    $card->stack->touch();

    $comment = new Comment($request->input('comment'));
    $comment->user = $request->user();
    $comment->meta = $card->getAuditHistory();
    $card->comments()->save($comment);

    $comment->attachments = $request->file('comment.attachment', []);

    $request->user()->follow($card);

    return redirect($card->uri);
  }

  public function check(Request $request, Comment $comment)
  {
    $lineNo = $request->input('line') - 1;
    $value = $request->input('value') ? 'x' : ' ';

    $lines = preg_split('/(\r\n|\r|\n)/', $comment->content);
    $line = $lines[$lineNo];

    if (preg_match('/^- \[(x| )\]/', $line)) {
      $lines[$lineNo] = substr_replace($line, $value, 3, 1);
    }

    $comment->content = implode("\r\n", $lines);
    $comment->save();
  }

}
