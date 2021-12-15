<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargeWallet extends Model
{
    use HasFactory;

    protected $table = 'recharge_wallet';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'document','tlf','cant'
    ];
}
