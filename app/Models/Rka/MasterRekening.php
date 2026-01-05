<?php

namespace App\Models\Rka;

use Illuminate\Database\Eloquent\Model;

class MasterRekening extends Model
{
    protected $connection = 'modul_anggaran';
    protected $table = 'master_rekenings';
    protected $guarded = ['id'];
}