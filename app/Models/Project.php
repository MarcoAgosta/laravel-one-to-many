<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Type;

class Project extends Model
{
    use HasFactory;
    protected $fillable=[
        "name", "description", "cover_img", "github_link"
    ];

    public function projectType(){
        return $this->hasMany(Type::class);
    }
}
