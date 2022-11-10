<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function files(){
        return $this->hasMany(File::class);
    }

    public function folders(){
        return $this->hasMany(Folder::class);
    }
    public function ratio_of_files_by_extensions(){
        return $this->hasMany(RatioOfFilesByExtension::class);
    }

    public function shared_contents(){
        return $this->hasMany(SharedContent::class);
    }

    public function getMaximumMemoryFormattedAttribute(){
        $maximum_memory_rounded = $this->maximum_memory/1000000000;
        return $maximum_memory_rounded;
    }
    public function getUsedMemoryFormattedAttribute(){
        $used_memory = File::query()->where('user_id', auth()->user()->id)->sum('file_size');
        $used_memory_formatted = number_format($used_memory/1000000, 2);
        return $used_memory_formatted;
    }
}
