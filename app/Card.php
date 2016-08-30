<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Attachment;
use App\Stack;
use App\Comment;
use App\User;
use App\Assignee;
use App\Follwer;
use Carbon\Carbon;
use App\ModelTraits\Auditable;

class Card extends Model
{

  use Auditable;

  protected $fillable = [
    'title',
    'description',
    'asignee_id',
  ];

  public function stack()
  {
    return $this->belongsTo(Stack::class);
  }

  public function comments()
  {
    return $this->morphMany(Comment::class, 'source');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function assignees()
  {
    return $this->morphToMany(User::class, 'assignee');
  }

  public function getUriAttribute()
  {
    return '/cards/'.$this->getKey();
  }

  public function setUserAttribute(User $user)
  {
    $this->attributes['user_id'] = $user->id;
  }

  public function setStackAttribute(Stack $stack)
  {
    $this->attributes['stack_id'] = $stack->id;
  }

  public function setAssigneeIdAttribute(array $userIds=null)
  {
    $this->auditSyncing('assignees', $userIds);
    $this->assignees()->sync($userIds);
  }

  public function followers()
  {
    return $this->morphMany(Follower::class, 'source');
  }

  public function allAttachments()
  {
    $card = $this;
    $cardType = get_class($this);
    $cardId = $this->id;

    return Attachment::orWhere(function($query) use ($cardType, $cardId) {
      $query->where('source_type', '=', $cardType);
      $query->where('source_id', '=', $cardId);
    })
    ->orWhere(function($query) use ($card) {
      $query->where('source_type', '=', Comment::class);
      $query->whereIn('source_id', $card->comments->pluck('id')->toArray());
    })->get();
  }

  public function getHotAttribute()
  {
    return $this->updated_at > Carbon::now()->subHour();
  }

  public function getCountTasksAttribute()
  {
    return preg_match_all('/^- \[(x| )\]/sm', $this->description);
  }

  public function getCountAllTasksAttribute()
  {
    return $this->countTasks + $this->comments->sum('countTasks');
  }

  public function getCountTasksCompleteAttribute()
  {
    return preg_match_all('/^- \[x\]/sm', $this->description);
  }

  public function getCountAllTasksCompleteAttribute()
  {
    return $this->countTasksComplete + $this->comments->sum('countTasksComplete');
  }

  public function getPercentTasksCompleteAttribute()
  {
    $complete = $this->countTasks;
    $total = $this->countTasksComplete;

    return $complete / $total;
  }

  public function getPercentAllTasksCompleteAttribute()
  {
    if ($this->countAllTasks > 0) {
      return $this->countAllTasksComplete / $this->countAllTasks;
    }

    return 0;
  }

}
