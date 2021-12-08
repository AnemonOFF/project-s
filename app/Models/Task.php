<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    protected $guarded = [];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    protected static function boot(){
        parent::boot();
        static::deleting(function(Task $taskToDelete){
            DB::table('marks')->where('task_id', '=', $taskToDelete->id)->delete();
        });
    }
}
