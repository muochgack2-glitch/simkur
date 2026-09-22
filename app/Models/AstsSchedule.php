<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AstsSchedule extends Model
{
    protected $fillable = ['kelas', 'jurusan', 'hari', 'sesi', 'mapel', 'nama_guru'];
}