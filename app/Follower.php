<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Follower extends Model
{

  protected $fillable = [
    'source_type',
    'source_id',
    'user_id'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

}
