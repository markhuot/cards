<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Card;
use App\Project;
use Elasticsearch\Client as Search;

class Tag extends Model
{

  protected $fillable = [
    'name',
  ];

  public function getLabelAttribute()
  {
    return $this->name;
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function getUriAttribute()
  {
    return '/projects/'.$this->project->id.'?tag:'.$this->name;
  }

  public function cards()
  {
    return $this->belongsToMany(Card::class);
  }

  public function search($q)
  {
    if (!$q) {
      return $this->cards;
    }

    $elasticsearch = resolve(Search::class);
    $result = $elasticsearch->search([
      "index" => "cards",
      "type" => "card",
      "body" => [
        "query" => [
          "query_string" => [
            "default_operator" => "AND",
            "query" => 'tag:'.$this->name.' AND ('.$q.')',
          ],
        ],
      ],
    ]);

    $ids = array_pluck($result['hits']['hits'], '_source.id');

    return Card::find($ids);
  }

}
