<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Stack;
use App\User;
use App\Tag;

class Project extends Model
{

  protected $fillable = [
    'name',
  ];

  public function getUriAttribute()
  {
    return '/projects/' . $this->getKey();
  }

  /**
   * A unique key, generated each time the comment updates
   */
  public function getCacheKeyAttribute()
  {
    return 'project-'.$this->id.'-'.$this->updated_at->timestamp;
  }

  public function stacks()
  {
    return $this->hasMany(Stack::class)->orderBy('order', 'asc');
  }

  public function users()
  {
    return $this->belongsToMany(User::class)->orderBy('name', 'asc');
  }

  public function cards()
  {
    return Card::whereIn('stack_id', $this->stacks->pluck('id'));
  }

  public function tags()
  {
    return $this->hasMany(Tag::class)->orderBy('name', 'asc');
  }

}
