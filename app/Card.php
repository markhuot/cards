<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Stack;
use App\Comment;
use App\User;
use App\Assignee;
use App\Follwer;

class Card extends Model
{

  protected $fillable = [
    'title',
    'description',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function($model) {
      if (!$model->order && $model->stack) {
        $model->order = $model->stack->cards()->max('order');
      }
    });
  }

  public function stack()
  {
    return $this->belongsTo(Stack::class);
  }

  public function getUriAttribute()
  {
    return '/cards/'.$this->getKey();
  }

  public function comments()
  {
    return $this->morphMany(Comment::class, 'source');
  }

  public function setUserAttribute(User $user)
  {
    $this->attributes['user_id'] = $user->id;
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function assignees()
  {
    return $this->morphMany(Asignee::class, 'source');
  }

  public function followers()
  {
    return $this->morphMany(Follower::class, 'source');
  }

}
