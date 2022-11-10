<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\ParentChildFolder;
use App\Models\SharedContent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreFolderRequest  $request
     //* @return \Illuminate\Http\Response
     */
    public function store(StoreFolderRequest $request)
    {
        $name_ind = Folder::assignFolderName($request->name, $request->folder_id);
        session()->put('name_ind', $name_ind);
        if(!$name_ind){
            return redirect()->route('file.index',['error_msg' => 'Name already exists!']);
        }

        $current_folder = Folder::query()->create([
            'name' => $request->name,
            'user_id' => auth()->user()->id
        ]);
        if($request->folder_id != null){
            $folder_id = $request->folder_id;
            Folder::query()->where('id', $current_folder->id)->update([
               'parent_id' => $folder_id
            ]);
        }
        return redirect()->route('file.index', ['error_msg' => '']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     //* @return \Illuminate\Http\Response
     */
    public function show(Folder $folder)
    {
        if(!$folder->authCheck()) return redirect()->route('file.index');
        $shared_users = SharedContent::query()->where('folder_id', $folder->id)->get();
        $shared_users_array = [];
        foreach ($shared_users as $user){
            $shared_users_array[] = $user->user_id;
        }

        return view('folders.show',
            [
                'file_children' => $folder->getFileChildren(),
                'folder_children' => $folder->getFolderChildren(),
                'folder' => $folder,
                'other_users' => User::query()->whereNot('id', auth()->user()->id)->whereNotIn('id', $shared_users_array)->get(),
                'shared_users' => $shared_users

            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function edit(Folder $folder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFolderRequest  $request
     * @param  \App\Models\Folder  $folder
     //* @return \Illuminate\Http\Response
     */
    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        if(!$folder->authCheck()) return redirect()->route('file.index');
        $name_ind = Folder::assignFolderName($request->name, $folder->parent_id);

        if($name_ind){
            Folder::query()->where('id', $folder->id)->update([
                'name' => $request->name
            ]);
        }else{
            return redirect()->route('file.index', ['error_msg' => 'Folder name already exists!']);
        }

        return redirect()->route('file.index', ['error_msg' => '']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folder  $folder
     //* @return \Illuminate\Http\Response
     */
    public function destroy(Folder $folder)
    {
        if(!$folder->authCheck()) return redirect()->route('file.index');
        DB::beginTransaction();
        $childrenFiles = File::query()->where('folder_id', $folder->id)->get();
        $childrenFolders = Folder::query()->where('parent_id', $folder->id)->get();
        foreach ($childrenFiles as $childF){
            if(!File::query()->where('id', $childF->id)->delete()) DB::rollBack();
        }
        foreach ($childrenFolders as $childFolder){
            if(!Folder::query()->where('id', $childFolder->id)->delete()){
                DB::rollBack();
                return redirect()->route('file.index', ['error_msg' => 'Error occurred!']);
            }
        }
        if($folder->shared_contents()->count()!=0){
            if(!$folder->shared_contents()->delete()) {
                DB::rollBack();
                return redirect()->route('file.index', ['error_msg' => 'Error occurred']);
            }
        }
        if(!$folder->delete()){
            DB::rollBack();
            return redirect()->route('file.index', ['error_msg' => 'Error occurred!']);
        }
        DB::commit();
        return redirect()->route('file.index', ['error_msg' => '']);
    }

    public function showChildren(Folder $folder){
        return  json_encode(array_merge($folder->getFileChildren(), $folder->getFolderChildren()));
    }

}
