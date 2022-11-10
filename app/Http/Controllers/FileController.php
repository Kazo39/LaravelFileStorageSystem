<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\Folder;
use App\Models\RatioOfFilesByExtension;
use App\Models\SharedContent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *

     */
    public function index(Request $request)
    {
        return view('files.index',[
            'files' => File::query()->where('user_id', auth()->user()->id)->where('folder_id', null)->get(),
            'folders' => Folder::query()->where('user_id', auth()->user()->id)->where('parent_id', null)->get(),
            'error_msg' => $request->error_msg
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFileRequest  $request
     //* @return \Illuminate\Http\Response
     */
    public function store(StoreFileRequest $request)
    {
        $folder_id = null;
        $user = auth()->user();
        if($request->has('folder_id')){
            $folder_id = $request->folder_id;
        }
        if (!Storage::exists('userFiles')) {
            Storage::makeDirectory('userFiles');
        }
        DB::beginTransaction();
        foreach ($request->file('files') as $file){
            $name_ind = File::assignFileName($file->getClientOriginalName(), $folder_id);
            $total_size = File::query()->sum('file_size');
            $total_size = $total_size + $file->getSize();
            if(RatioOfFilesByExtension::query()->where('extension', $file->getClientOriginalExtension())->where('user_id', $user->id)->get()->toArray() == [] ){
                RatioOfFilesByExtension::query()->create([
                    'extension' => $file->getClientOriginalExtension(),
                    'user_id' => $user->id
                ]);
            }

            if($name_ind && $total_size < $user->maximum_memory){
                 auth()->user()->files()->create([
                    'path' => Storage::put('userFiles', $file),
                    'user_id' => $user->id,
                    'folder_id' => $folder_id,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize()
                ]);
                $current_size= RatioOfFilesByExtension::query()->where('extension', $file->getClientOriginalExtension())
                    ->where('user_id',auth()->user()->id)
                    ->sum('total_size');
                $new_size =  $current_size + $file->getSize();

                RatioOfFilesByExtension::query()->where('extension', $file->getClientOriginalExtension())->where('user_id', $user->id)->update([
                    'total_size' => $new_size
                ]);
            }else{
                DB::rollBack();
                return redirect()->route('file.index', ['error_msg' => 'You are out of memory or file name already exists!']);
            }
        }
        DB::commit();
        return redirect()->route('file.index', ['error_msg' => '']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     //* @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        if(!$file->authCheck()) return redirect()->route('file.index');
        return view('files.show', [
            'file' => $file,
            'other_users' => User::query()->whereNot('id', auth()->user()->id)->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFileRequest  $request
     * @param  \App\Models\File  $file
     //* @return \Illuminate\Http\Response
     */
    public function update(UpdateFileRequest $request, File $file){
        if(!$file->authCheck()) return redirect()->route('file.index');
        $file_name = $request->name.'.'.$file->file_type;
        $name_ind = File::assignFileName($file_name, $file->folder_id);

        if($name_ind){
            File::query()->where('id', $file->id)->update([
                'file_name' => $request->name.'.'.$file->file_type
            ]);
        }else{
            return redirect()->route('file.index', ['error_msg' => 'Name already exists!']);
        }
        return redirect()->route('file.index', ['error_msg' => ''] );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     //* @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        if(!$file->authCheck()) return redirect()->route('file.index');
        DB::beginTransaction();

        if($file->shared_contents()->count()!=0){
            if(!$file->shared_contents()->delete()) {
                DB::rollBack();
                return redirect()->route('file.index', ['error_msg' => 'Error occurred']);
            }
        }
        if(!$file->delete()){
            DB::rollBack();
            return redirect()->route('file.index', ['error_msg' => 'Error occurred']);
        }
        $extension_size = RatioOfFilesByExtension::query()->where('user_id', auth()->user()->id)->where('extension', $file->file_type)->sum('total_size');
        $extension_size = $extension_size - $file->file_size;

        if($extension_size == 0){
            if(!RatioOfFilesByExtension::query()->where('user_id', auth()->user()->id)->where('extension', $file->file_type)->delete()){
                DB::rollBack();
                return redirect()->route('file.index', ['error_msg' => 'Error occurred']);
            }
        }else{
            RatioOfFilesByExtension::query()->where('user_id', auth()->user()->id)->where('extension', $file->file_type)->update([
                'total_size' => $extension_size
            ]);
        }


        if(!Storage::delete($file->path)){
            DB::rollBack();
            return redirect()->route('file.index', ['error_msg' => 'Error occurred']);
        }
        DB::commit();
        return redirect()->route('file.index', ['error_msg' => '']);
    }
    public function downloadFile(File $file){
        return Storage::download($file->path);
    }

}
