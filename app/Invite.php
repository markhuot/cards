<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Project;
use App\User;

class Invite extends Model
{

  public function inviter()
  {
    return $this->belongsTo(User::class);
  }

  public function invitee()
  {
    return $this->belongsTo(User::class);
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

}
