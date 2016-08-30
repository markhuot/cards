<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attachment;
use App\User;
use App\Stack;

class Comment extends Model
{

  protected $fillable = [
    'content',
  ];

  protected $touches = [
    'source',
  ];

  /**
   * The source of our comment, usually a card. Maybe more things will
   * have comments in the future though, such as attachments.
   *
   * @return Relation
   */
  public function source()
  {
    return $this->morphTo();
  }

  /**
   * The user who left the comment
   *
   * @return Relation
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Set the user_id by an object, instead of forcing the controller
   * to interact with the raw field names.
   *
   * @param User $user
   */
  public function setUserAttribute(User $user)
  {
    $this->attributes['user_id'] = $user->id;
  }

  /**
   * Any attachments tied to this comment
   *
   * @return Relation
   */
  public function attachments()
  {
    return $this->morphMany(Attachment::class, 'source');
  }

  /**
   * Set the attachments based on an array of proxy objects. These
   * will typically come from a Request::file().
   *
   * @param array $attachments
   */
  public function setAttachmentsAttribute(array $attachments)
  {
    foreach (array_filter($attachments) as $file) {
      $attachment = new Attachment;
      $attachment->user = app('request')->user();
      $attachment->type = 'image';
      $attachment->link = $file->store('attachments');
      $this->attachments()->save($attachment);
    }
  }

  /**
   * Number of tasks within this comment
   *
   * @return int
   */
  public function getCountTasksAttribute()
  {
    return preg_match_all('/^- \[(x| )\]/sm', $this->content);
  }

  /**
   * Number of tasks that are complete for this comment
   *
   * @return int
   */
  public function getCountTasksCompleteAttribute()
  {
    return preg_match_all('/^- \[x\]/sm', $this->content);
  }

  /**
   * The percent of tasks that are complete for this comment
   *
   * @return float
   */
  public function getPercentTasksCompleteAttribute()
  {
    return $this->countTasksComplete / $this->countTasks;
  }

  /**
   * Store the meta data of the comment by serializing an array
   * down to JSON.
   *
   * @param array $meta
   */
  public function setMetaAttribute(array $meta)
  {
    $this->attributes['meta'] = json_encode($meta);
  }

  /**
   * Pull the meta data out of the comment and re-hydrate any
   * objects on the meta
   *
   * @return array
   */
  public function getMetaAttribute()
  {
    $changes = json_decode($this->attributes['meta'], true);

    foreach ($changes as &$change) {
      switch ($change['key']) {
        case 'assignees':
          $change['label'] = 'Assignees';
          $change['added'] = User::find($change['added']);
          $change['removed'] = User::find($change['removed']);
          break;
        case 'stack_id':
          $change['label'] = 'Stack';
          $change['added'] = [Stack::find($change['added'])];
          $change['removed'] = [Stack::find($change['removed'])];
          break;
      }
    }

    return $changes;
  }

}
