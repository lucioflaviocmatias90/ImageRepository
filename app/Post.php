<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'description', 'image'];
    protected $table = 'posts';
    public $timestamps = true;
    protected $casts = [
    	'image'=>'array',
    	'cover'=>'array',
    ];
}
