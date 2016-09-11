<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Card;
use App\Project;
use Elasticsearch\Client as Search;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'name',
    'project_id',
  ];

  public function getLabelAttribute()
  {
    return $this->name;
  }

  public function getCacheKeyAttribute()
  {
    return 'tag-'.$this->id.'-'.$this->updated_at->timestamp;
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function getUriAttribute()
  {
    return '/tags/'.$this->id;
  }

    public function getSearchKey()
    {
      return 'tag_id';
    }

  public function cards()
  {
    return $this->belongsToMany(Card::class)->orderBy('order', 'asc');
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
