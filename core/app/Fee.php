<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'fees';
    protected $primaryKey = 'id';
    protected $fillable = array( 'btc_balance', 'dmc_balance', 'oro_balance', 'free_balance');
}