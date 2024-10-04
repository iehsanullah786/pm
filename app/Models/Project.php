<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relationship with ProjectFile
    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }
}
