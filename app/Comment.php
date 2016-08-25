<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attachment;
use App\User;

class Comment extends Model
{

  protected $fillable = [
    'content',
  ];

  protected $touches = [
    'source',
  ];

  public function source()
  {
    return $this->morphTo();
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function setUserAttribute(User $user)
  {
    $this->attributes['user_id'] = $user->id;
  }

  public function attachments()
  {
    return $this->morphMany(Attachment::class, 'source');
  }

}
