<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Comment;
use App\Card;

class CommentController extends Controller
{

  public function store(Request $request, Card $card)
  {
    $comment = new Comment($request->input('comment', []));
    $comment->user = $request->user();
    $card->comments()->save($comment);

    $request->user()->follow($card);

    return redirect($card->uri);
  }

}
