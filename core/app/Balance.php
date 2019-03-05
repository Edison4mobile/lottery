<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['user_id','btc_address','btc_balance','oro_address',
        'oro_balance','dmc_address','dmc_balance','free_balance'];
}
