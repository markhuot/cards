<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Attachment;
use App\Stack;
use App\Comment;
use App\User;
use App\Assignee;
use App\Follower;
use App\Tag;
use Carbon\Carbon;
use App\ModelTraits\Auditable;

class Card extends Model
{

  use Auditable;

  protected $fillable = [
    'title',
    'description',
    'asignee_id',
    'complete',
  ];

  protected static function boot()
  {
    static::creating(function($model) {
      if (!$model->local_id) {
        $localId = $model->stack->project->cards()->max('local_id');
        if ($localId == false) {
          $model->local_id = 1000;
        }
        else {
          $model->local_id = $localId + 1;
        }
      }
    });

    static::saved(function($model) {
      $model->saveToSearchIndex();
    });
  }

  public function saveToSearchIndex()
  {
    $elasticsearch = resolve(\Elasticsearch\Client::class);
    $result = $elasticsearch->index([
      'index' => 'cards',
      'type' => 'card',
      'id' => 'card:'.$this->getKey(),
      'body' => $this->toSearchableArray(),
    ]);
  }

  public function toSearchableArray()
  {
    $array = [];

    $array['text'] = collect($this->toArray())->only([
      'title',
      'description',
    ]);

    $array['id'] = $this->id;
    $array['stack'] = $this->stack->name;
    $array['stack_id'] = $this->stack->id;
    $array['assignee'] = $this->assignees->pluck('name');
    $array['tag'] = $this->tags->pluck('name');

    $array['is'] = [];
    if ($this->complete) {
      $array['is'][] = 'complete';
    }

    $array['has'] = [];
    if ($this->attachments->count() > 0) {
      $array['has'][] = 'attachments';
    }
    if ($this->comments->count() > 0) {
      $array['has'][] = 'comments';
    }

    return $array;
  }

  public function getLabelAttribute()
  {
    return $this->title;
  }

  public function getCacheKeyAttribute()
  {
    return 'card-'.$this->id.'-'.$this->updated_at->timestamp;
  }

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

  public function tags()
  {
    return $this->belongsToMany(Tag::class);
  }

  public function getTagStringAttribute()
  {
    return $this->tags->pluck('name')->implode(' ');
  }

  public function setTagsByString(string $tagString)
  {
    $projectId = $this->stack->project->getKey();

    // collect the tags, creating new ones where necessary
    $tags = collect(array_filter(preg_split('/\s+/', $tagString)))->map(function ($tagName) use ($projectId) {
      return Tag::firstOrCreate([
        'name' => $tagName,
        'project_id' => $projectId
      ]);
    });

    // keep an audit log of what's changing
    $this->auditSyncing('tags', $tags->pluck('id')->all());

    // touch each of the tags that is changing
    $attaching = $tags->diff($this->tags);
    $detaching = $this->tags->diff($tags);
    $attaching->merge($detaching)->each(function ($tag) {
      $tag->touch();
    });
    
    // save the changes to our pivot table
    $this->tags()->sync($tags->pluck('id')->all());

    // refresh our relationship and save it to the search index
    // @TODO probably don't need to `load` here since we have all the tags above. I wonder
    // if, instead, we can just assign `$this->tags = $tags`
    $this->load('tags');
    $this->saveToSearchIndex();
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

  public function setAssigneesById(array $assigneeIds)
  {
    // save an audit log of the change
    $this->auditSyncing('assignees', $assigneeIds);

    // grab the desired list of user objects
    $users = User::find($assigneeIds);

    // touch each of the users that is changing
    $attaching = $users->diff($this->assignees);
    $detaching = $this->assignees->diff($users);
    $attaching->merge($detaching)->each(function ($user) {
      $user->touch();
    });

    // store the assignees
    $this->assignees()->sync($assigneeIds);

    // load the relationship and save it to the search index
    $this->load('assignees');
    $this->saveToSearchIndex();
  }

  public function followers()
  {
    return $this->morphMany(Follower::class, 'source');
  }

  /**
   * Any attachments tied to this card
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
