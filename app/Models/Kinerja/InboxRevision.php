<?php
namespace App\Models\Kinerja;

use Illuminate\Database\Eloquent\Model;

class InboxRevision extends Model
{
    protected $connection = 'modul_kinerja';
    protected $table = 'inbox_revisions';

    protected $fillable = [
        'sub_activity_id', 'pengirim_nip', 'penerima_nip', 'catatan_revisi', 'status_revisi'
    ];

    public function subActivity()
    {
        return $this->belongsTo(SubActivity::class, 'sub_activity_id');
    }
}