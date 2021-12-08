<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Block extends Model
{
    protected $guarded = [];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected static function boot(){
        parent::boot();
        static::deleting(function(Block $blockToDelete){
            Task::where('block_id', '=', $blockToDelete->id)->get()->each(function(Task $task){
                $task->delete();
            });
        });
    }
}
