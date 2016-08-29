<?php namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Requests\StoreCardComment;
use App\Attachment;
use App\Comment;
use App\Card;
use App\User;
use App\Stack;

class CommentController extends Controller
{

  public function store(StoreCardComment $request, Card $card)
  {
    $card->assignee_id = $request->input('card.assignee_id', []);
    $card->stack_id = $request->input('card.stack_id');
    $card->save();

    $comment = new Comment($request->input('comment'));
    $comment->user = $request->user();
    $card->comments()->save($comment);

    $comment->attachments = $request->file('comment.attachment', []);

    $request->user()->follow($card);

    return redirect($card->uri);
  }

}
