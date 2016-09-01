<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Stack;
use App\User;

class Project extends Model
{

  protected $fillable = [
    'name',
  ];

  public function getUriAttribute()
  {
    return '/projects/' . $this->getKey();
  }

  public function stacks()
  {
    return $this->hasMany(Stack::class);
  }

  public function users()
  {
    return $this->belongsToMany(User::class)->orderBy('name', 'asc');
  }

  public function cards()
  {
    return Card::whereIn('stack_id', $this->stacks->pluck('id'));
  }

}
