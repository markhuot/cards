<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Stack;

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

}
