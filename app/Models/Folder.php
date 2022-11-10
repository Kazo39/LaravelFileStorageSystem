<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Folder extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shared_contents(){
        return $this->hasMany(SharedContent::class);
    }

    public function FileSizeFormatted($file_size){
        $file_size_formatted = null;
        if($file_size < 1000){
            $file_size_formatted =  $file_size;
        }elseif ($file_size > 1000 && $file_size < 1000000){
            $file_size_formatted = number_format($file_size/1000, 0);
        }elseif ($file_size > 1000000 && $file_size < 1000000000){
            $file_size_formatted = number_format($file_size/1000000, 0);
        }
        return  $file_size_formatted;
    }

    public function FileSizeUnit($file_size){
        $file_size_unit = null;
        if($file_size < 1000){
            $file_size_unit = 'bytes';
        }elseif ($file_size > 1000 && $file_size < 1000000){
            $file_size_unit = 'KB';
        }elseif ($file_size > 1000000 && $file_size < 1000000000){
            $file_size_unit = 'MB';
        }
        return  $file_size_unit;
    }

    public function getFileChildren(){

        $file_children =  File::query()->where('folder_id', $this->id)->get()->toArray();
        foreach ($file_children as &$child){
            $file_size_formatted = $this->FileSizeFormatted($child['file_size']);
            $file_size_unit = $this->FileSizeUnit($child['file_size']);
            $date_modified = date_format(date_create($child['updated_at']), 'd.m.Y H:i');

            $child['file_size_formatted'] = $file_size_formatted;
            $child['file_size_unit'] = $file_size_unit;
            $child['date_modified'] = $date_modified;
        }
        return $file_children;
    }

    public function getFolderChildren(){
        $folder_children = Folder::query()->where('parent_id', $this->id)->get()->toArray();
        foreach ($folder_children as &$child){
            $date_modified = date_format(date_create($child['updated_at']), 'd.m.Y H:i');
            $child['date_modified'] = $date_modified;
        }
        return $folder_children;
    }

    public static function assignFolderName($name, $parent_folder_id){
        if($parent_folder_id != null){
            $other_children = Folder::query()->where('parent_id', $parent_folder_id)->where('user_id', auth()->user()->id)->get();

            foreach ($other_children as $other_child){
                if($other_child->name == $name) return false;
            }
            return true;
        }
        else{
            $other_children = Folder::query()->where('name', $name)->where('parent_id', null)->where('user_id', auth()->user()->id)->get()->toArray();

            if($other_children == []) return true;
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
