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

    public function students()
    {
        return $this->belongsToMany(Student::class, 'courses_students', 'course_id', 'student_id');
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
