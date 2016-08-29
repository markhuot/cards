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

  public function setAttachmentsAttribute(array $attachments)
  {
    foreach ($attachments as $file) {
      $path = $file->store('attachments');
      $attachment = new Attachment;
      $attachment->source_type = get_class($this);
      $attachment->source_id = $this->id;
      $attachment->user = app('request')->user();
      $attachment->type = 'image';
      $attachment->link = $path;
      $this->attachments()->save($attachment);
    }
  }

  public function getCountTasksAttribute()
  {
    return preg_match_all('/^- \[(x| )\]/sm', $this->content);
  }

  public function getCountTasksCompleteAttribute()
  {
    return preg_match_all('/^- \[x\]/sm', $this->content);
  }

  public function getPercentTasksCompleteAttribute()
  {
    return $this->countTasksComplete / $this->countTasks;
  }


}
