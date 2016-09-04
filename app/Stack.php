<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Project;
use App\Card;
use Elasticsearch\Client as Search;

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
    return $this->hasMany(Card::class)->orderBy('order', 'asc');
  }

  public function openCards()
  {
    return $this->hasMany(Card::class)->where('complete', '=', false)->orderBy('order', 'asc');
  }

  public function search($q)
  {
    if (!$q) {
      return $this->openCards;
    }

    $elasticsearch = resolve(Search::class);
    $result = $elasticsearch->search([
      "index" => "cards",
      "type" => "card",
      "body" => [
        "query" => [
          "query_string" => [
            "default_operator" => "AND",
            "query" => 'stack_id:'.$this->id.' AND ('.$q.')',
          ],
        ],
      ],
    ]);

    $ids = array_pluck($result['hits']['hits'], '_source.id');

    return Card::find($ids);
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
