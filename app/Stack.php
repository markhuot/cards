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
    return 'projects/' . $this->project->getKey().'/stacks/'.$this->getKey();
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function cards()
  {
    return $this->hasMany(Card::class);
  }

}
