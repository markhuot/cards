<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Attachment;
use App\Comment;
use App\Card;

class CommentController extends Controller
{

  public function store(Request $request, Card $card)
  {
    $comment = new Comment($request->input('comment', []));
    $comment->user = $request->user();
    $card->comments()->save($comment);

    if (($attachments=$request->file('comment.attachment')) && count(array_filter($attachments)) > 0) {
      foreach ($attachments as $file) {
        $path = $file->store('attachments/project/'.$card->stack->project->id);
        $attachment = new Attachment;
        $attachment->source_type = get_class($comment);
        $attachment->source_id = $comment->id;
        $attachment->user = $request->user();
        $attachment->type = 'image';
        $attachment->link = $path;
        $comment->attachments()->save($attachment);
      }
    }

    $request->user()->follow($card);

    return redirect($card->uri);
  }

}
