<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attachment;
use App\Stack;
use App\Comment;
use App\User;
use App\Assignee;
use App\Follwer;
use Carbon\Carbon;

class Card extends Model
{

  protected $fillable = [
    'title',
    'description',
    'asignee_id',
  ];

  protected static function boot()
  {
    parent::boot();

    // static::creating(function($model) {
    //   if (!$model->order && $model->stack) {
    //     $model->order = $model->stack->cards()->max('order');
    //   }
    // });
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
    return $this->morphMany(Assignee::class, 'source');
  }

  public function setAssigneeIdAttribute(array $userIds=null)
  {
    $sourceType = get_class($this);
    $sourceId = $this->id;
    $assignees = collect($userIds)->map(function($userId) use ($sourceType, $sourceId) {
      return Assignee::firstOrNew([
        'source_type' => $sourceType,
        'source_id' => $sourceId,
        'user_id' => $userId,
      ]);
    });
    $this->assignees()->saveMany($assignees);
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
