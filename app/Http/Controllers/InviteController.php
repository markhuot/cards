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
    $this->middleware('auth');
  }

  public function create(Project $project)
  {
    return view('invite.create')
      ->with('project', $project)
    ;
  }

  public function store(Request $request, Project $project)
  {
    $email = $request->input('invite.email');

    $invite = new Invite;
    $invite->inviter_id = $request->user()->id;
    $invite->project_id = $project->id;
    $invite->invitee_email = $email;
    $invite->hash = str_random(32);
    $invite->save();

     Mail::to($email)->send(new InviteUser($invite));

    return redirect($project->uri);
  }

  public function join(Request $request, $hash)
  {
    $invite = Invite::where('hash', '=', $hash)->firstOrFail();
    if ($invite->invitee_email != $request->user()->email) {
      abort(401, 'Email mismatch');
    }

    $invite->project->users()->attach($request->user());
    $invite->delete();

    return redirect($invite->project->uri);
  }

}
