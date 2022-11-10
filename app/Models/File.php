<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shared_contents(){
        return $this->hasMany(SharedContent::class);
    }

    public function getFileSizeFormattedAttribute(){
        $file_size_formatted = null;
        if($this->file_size < 1000){
            $file_size_formatted =  $this->file_size;
        }elseif ($this->file_size > 1000 && $this->file_size < 1000000){
            $file_size_formatted = number_format($this->file_size/1000, 0);
        }elseif ($this->file_size > 1000000 && $this->file_size < 1000000000){
            $file_size_formatted = number_format($this->file_size/1000000, 0);
        }
        return  $file_size_formatted;
    }

    public function getFileSizeUnitAttribute(){
        $file_size_unit = null;
        if($this->file_size < 1000){
            $file_size_unit = 'bytes';
        }elseif ($this->file_size > 1000 && $this->file_size < 1000000){
            $file_size_unit = 'KB';
        }elseif ($this->file_size > 1000000 && $this->file_size < 1000000000){
            $file_size_unit = 'MB';
        }
        return  $file_size_unit;
    }

    public function getDateModifiedAttribute(){
        $date_modified = $this->updated_at->format('d.m.Y H:i');
        return $date_modified;
    }

    public static function assignFileName($name, $folder_id)
    {
        if ($folder_id != null) {
            $other_children = File::query()->where('folder_id', $folder_id)->where('user_id', auth()->user()->id)->get();

            foreach ($other_children as $other_child){
                if ($other_child->file_name == $name) return false;
            }
            return true;
        } else {
            $other_children = File::query()->where('file_name', $name)->where('folder_id', null)->where('user_id', auth()->user()->id)->get()->toArray();

            if ($other_children == []) return true;
            return false;
        }
    }

    public function authCheck(){
        if($this->user_id != auth()->user()->id){
            return false;
        }
        return true;
    }

}
