<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    protected static function boot(){
        parent::boot();
        static::deleting(function(Course $courseToDelete){
            Block::where('course_id', '=', $courseToDelete->id)->get()->each(function(Block $block){
                $block->delete();
            });;
        });
    }
}
