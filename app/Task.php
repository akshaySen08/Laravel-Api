<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use App\Task;

class Task extends Model
{
    protected $table ='tasks';

    protected $fillable = [
        'task',
        'user',
        'status',
        'date_added',
        'date_completed'
    ];

    public $timestamps = false;

    private function user()
    {
        return $this->belongsTo('App\User');
    }
}
