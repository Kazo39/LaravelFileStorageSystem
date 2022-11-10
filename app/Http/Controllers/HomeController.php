<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAllowedMemoryRequest;
use App\Models\File;
use App\Models\RatioOfFilesByExtension;
use App\Models\SharedContent;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $used_memory = File::query()->where('user_id', auth()->user()->id)->sum('file_size');
        $used_memory_percentage = ($used_memory / auth()->user()->maximum_memory)*100;
        $files_by_extension = RatioOfFilesByExtension::query()->where('user_id', auth()->user()->id)->orderBy('total_size')->get()->toArray();
        $temp_memory = 0;
        $other_files_array = [];

        foreach ($files_by_extension as $file){
             $temp_memory += $file['total_size'];
            if($temp_memory < ($used_memory/100)*5){
                $other_files_array[] = $file;
                array_shift($files_by_extension);
            }
        }
        $other_files_total_size = 0;
        foreach ($other_files_array as $other_file){
            $other_files_total_size += $other_file['total_size'];
        }
        $last_three_shared_files_folders = SharedContent::query()->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->limit(3)->get();
        return view('home', [
            'used_memory_percentage' => $used_memory_percentage,
            'files_by_extension' => $files_by_extension,
            'files_below_5_percentage' => $other_files_array,
            'files_below_5_percentage_total_size' => $other_files_total_size,
            'last_three_shared_files_folders' => $last_three_shared_files_folders
        ]);
    }

    public function adminPage(){
        return view('admin_page', [
           'users' => User::all()
        ]);
    }
    public function updateAllowedMemory(UpdateAllowedMemoryRequest $request){
        $new_memory = $request->new_memory * 1000000000;
        User::query()->where('id', $request->user_id)->update([
            'maximum_memory' => $new_memory
        ]);

        return redirect()->route('admin-page');
    }
}
