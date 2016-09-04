<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{

  public function setUserAttribute(User $user)
  {
    $this->attributes['user_id'] = $user->id;
  }

  public function getUriAttribute()
  {
    return '/attachments/'.$this->getKey();
  }

}
