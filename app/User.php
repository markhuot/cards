<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Project;

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
}
