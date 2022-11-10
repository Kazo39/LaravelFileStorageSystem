<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedContent extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function file(){
        return $this->belongsTo(File::class);
    }

    public function folder(){
        return $this->belongsTo(Folder::class);
    }

}
