<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';
    protected $primaryKey = 'id';
    protected $fillable = array( 'data', 'status', 'coin', 'result', 'started_at', 'ended_at');
}
