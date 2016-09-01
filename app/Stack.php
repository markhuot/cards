<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Project;
use App\Card;

class Stack extends Model
{
  protected $fillable = [
    'name',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function($model) {
      if (!$model->color) {
        $model->color = str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
      }
    });
  }

  public function getUriAttribute()
  {
    return '/projects/' . $this->project->getKey().'/stacks/'.$this->getKey();
  }

  public function getLabelAttribute()
  {
    return $this->name;
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function cards()
  {
    $relation = $this->hasMany(Card::class)->orderBy('order', 'asc');

    if ($q=app('request')->q) {
      if (substr($q, 0, 4) == 'tag:') {
        $tagName = substr($q, 4);
        $relation->whereHas('tags', function($query) use ($tagName) {
          $query->where('name', '=', $tagName);
        });
      }
    }

    return $relation;
  }

  public function getCardsCountAttribute()
  {
    return $this->cards->count();
  }

  public function size()
  {
    $max = $this->project->stacks->pluck('cards_count')->max();
    if ($max == 0) {
      return 0;
    }

    return $this->cards_count / $max;
  }

}
