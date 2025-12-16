<?php
namespace App\Models\Kinerja;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerja extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'indikator_kinerja';
    protected $guarded = ['id'];
}