<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Assignee extends Model
{

  protected $fillable = [
    'source_type',
    'source_id',
    'user_id'
  ];

  public function cards()
  {
    return $this->morphTo();
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

}
