<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $guarded = [];

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    protected static function boot(){
        parent::boot();
        static::deleting(function(Student $studentToDelete){
            DB::table('marks')->where('student_id', '=', $studentToDelete->id)->delete();
        });
    }
}
