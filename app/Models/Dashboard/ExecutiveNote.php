<?php
namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class ExecutiveNote extends Model {
    protected $connection = 'modul_dashboard';
    protected $fillable = ['sub_activity_id', 'catatan_instruksi', 'prioritas', 'status_tindak_lanjut'];
}