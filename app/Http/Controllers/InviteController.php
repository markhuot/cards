<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Mail\InviteUser;
use App\Project;
use App\User;
use App\Invite;

class InviteController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth')->except('join');
  }

  public function create(Project $project)
  {
    return view('invite.create')
      ->with('project', $project)
    ;
  }

  public function store(Request $request, Project $project)
  {
    $user = new User;
    $user->email = $request->input('invite.email');
    $user->password = str_random(32);
    $user->save();

    $invite = new Invite;
    $invite->inviter_id = $request->user()->id;
    $invite->invitee_id = $user->id;
    $invite->project_id = $project->id;
    $invite->hash = str_random(32);
    $invite->save();

     Mail::to($user)->send(new InviteUser($invite));

    return redirect($project->uri);
  }

  public function join(Request $request, $hash)
  {
    if (!$request->user()) {
      return redirect('auth/register/'.$hash);
    }

    $invite = Invite::where('hash', '=', $hash)->firstOrFail();
    if ($invite->invitee->id != $request->user()->id) {
      abort(404);
    }

    $invite->project->users()->attach($invite->invitee);
    $invite->delete();

    return redirect($invite->project->uri);
  }

}
