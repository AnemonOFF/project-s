<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    protected $guarded = [];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    protected static function boot(){
        parent::boot();
        static::deleting(function(Platform $platformToDelete){
            Course::where('platform_id', '=', $platformToDelete->id)->get()->each(function(Course $course){
                $course->delete();
            });;
        });
    }
}
