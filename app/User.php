<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Project;
use Elasticsearch\Client as Search;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Gets the user initials in uppercase
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
      return collect(array_filter(preg_split('/\s+/', $this->name)))
        ->map(function($name) {
          return strtoupper(substr($name, 0, 1));
        })
        ->implode('')
      ;
    }

    public function projects()
    {
      return $this->belongsToMany(Project::class);
    }

    public function unfollow($source)
    {
      $follower = Follower::where([
        'source_type' => get_class($source),
        'source_id' => $source->id,
        'user_id' => $this->id,
      ])->firstOrFail();
      $follower->delete();

      return $this;
    }

    public function follow($source)
    {
      $follower = Follower::firstOrCreate([
        'source_type' => get_class($source),
        'source_id' => $source->id,
        'user_id' => $this->id,
      ]);

      return $this;
    }

    public function getUriAttribute()
    {
      return '/users/'.$this->id;
    }

    public function getLabelAttribute()
    {
      return $this->name;
    }

    public function getColorAttribute()
    {
      // $h = collect(array_map('ord', preg_split('//', $this->name)))->sum() % 360;
      // $s = 80;
      // $l = 30;
      // return "hsl({$h}, {$s}%, {$l}%)";

      $colors = [];
      $colors[] = "#FFB300"; // Vivid Yellow
      $colors[] = "#803E75"; // Strong Purple
      $colors[] = "#FF6800"; // Vivid Orange
      $colors[] = "#A6BDD7"; // Very Light Blue
      $colors[] = "#C10020"; // Vivid Red
      $colors[] = "#CEA262"; // Grayish Yellow
      $colors[] = "#817066"; // Medium Gray
      $colors[] = "#007D34"; // Vivid Green
      $colors[] = "#F6768E"; // Strong Purplish Pink
      $colors[] = "#00538A"; // Strong Blue
      $colors[] = "#FF7A5C"; // Strong Yellowish Pink
      $colors[] = "#53377A"; // Strong Violet
      $colors[] = "#FF8E00"; // Vivid Orange Yellow
      $colors[] = "#B32851"; // Strong Purplish Red
      $colors[] = "#F4C800"; // Vivid Greenish Yellow
      $colors[] = "#7F180D"; // Strong Reddish Brown
      $colors[] = "#93AA00"; // Vivid Yellowish Green
      $colors[] = "#593315"; // Deep Yellowish Brown
      $colors[] = "#F13A13"; // Vivid Reddish Orange
      $colors[] = "#232C16"; // Dark Olive Green

      $index = collect(array_map('ord', preg_split('//', $this->name)))->sum() % count($colors);
      return $colors[$index];
    }

    public function cards()
    {
      return $this->morphedByMany(Card::class, 'assignee')->orderBy('order', 'asc');
    }

    public function getSearchKey()
    {
      return 'assignee_id';
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
            "query" => 'assignee:'.$this->name.' AND ('.$q.')',
          ],
        ],
      ],
    ]);

    $ids = array_pluck($result['hits']['hits'], '_source.id');

    return Card::find($ids);
  }
}
