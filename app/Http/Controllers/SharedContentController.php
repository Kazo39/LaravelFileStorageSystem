<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSharedContentRequest;
use App\Mail\ContentSharedMail;
use App\Models\File;
use App\Models\Folder;
use App\Models\SharedContent;
use App\Models\User;
use App\Notifications\sharedContentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SharedContentController extends Controller
{
    public function store(StoreSharedContentRequest $request){
        $parent_id = null;
        if($request->content_type == 'file'){
            $content = File::query()->where('id', $request->content_id)->get();
            $col = 'file_id';
            foreach ($content as $c){
                $parent_id = $c->folder_id;
            }
        }else{
            $content = Folder::query()->where('id', $request->content_id)->get();
            $col = 'folder_id';
            foreach ($content as $c){
                $parent_id = $c->folder_id;
            }
        }

        SharedContent::query()->create([
            'user_id' => $request->shared_user_id,
            $col => $request->content_id,
            'parent_id' => $parent_id,
            'content_type' => $request->content_type
        ]);

        $user_temp = User::query()->where('id', $request->shared_user_id)->get();
        foreach ($user_temp as $user){
            $user_ = $user;
        }

        $user_->notify(new sharedContentNotification($user_,auth()->user()));

        return redirect()->route('file.index');
    }

    public function index()
    {
        return view('shared-content.index',[
            'files' => SharedContent::query()->where('user_id', auth()->user()->id)->where('parent_id', null)->where('content_type', 'file')->get(),
            'folders' => SharedContent::query()->where('user_id', auth()->user()->id)->where('parent_id', null)->where('content_type', 'folder')->get()
        ]);
    }

    public function destroy(Request $request){
       SharedContent::query()->where('user_id', $request->user_id)->where('folder_id', $request->folder_id)->delete();
       return redirect()->route('file.index');
    }
}
