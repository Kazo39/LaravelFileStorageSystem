@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" >
                    <div class="card-header">
                        {{$folder->name}}
                        <a type="button" class="btn  float-end btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteFolderModal" >Delete</a>
                        <a type="button" class="btn btn-outline-primary  btn-sm float-end me-2" data-bs-toggle="modal" data-bs-target="#renameFolderModal" >Rename</a>
                        <a type="button" class="btn btn-outline-primary  btn-sm float-end me-2" data-bs-toggle="modal" data-bs-target="#sharedFolderModal" >Shared</a>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            @foreach($file_children as $fileC)

                                <div class="row">
                                    <div class="col-12">
                                        <i class="fa-solid fa-file ms-3"></i>
                                        <a class="ms-2" href="{{route('file.download',['file' => $fileC['id']])}}">{{$fileC['file_name']}}</a>
                                    </div>
                                </div>
                            @endforeach
                            @foreach($folder_children as $folderC)
                                <div class="row">
                                    <div class="col-10">
                                        <i class="fa-solid fa-folder" ></i>
                                        <span class="ms-2 " role="button">{{$folderC['name']}}</span>
                                    </div>
                                    <div class="col-2 ">
                                        <a href="{{route('folder.show', ['folder' => $folderC['id']])}}">
                                            <i class="fa-solid fa-circle-plus float-end" ></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row mt-4">
                            <form action="{{route('shared-content.store')}}" method="POST">
                                <div class="col-6 offset-3">

                                    @csrf
                                    <input type="hidden" name="content_id" value="{{$folder->id}}">
                                    <input type="hidden" name="content_type" value="folder">
                                    <select name="shared_user_id" id="shared_user_id" class="form-control">
                                        <option value="" selected disabled>-- share folder --</option>
                                        @foreach($other_users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <button class="btn mt-2 btn-success">Share</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Folder Modal -->
        <div class="modal fade" id="renameFolderModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Dodavanje foldera</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('folder.update', ['folder' => $folder])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="text" name="name"  class="form-control" value="{{$folder->name}}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button  class="btn btn-primary" >Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Shared Folder Modal -->
        <div class="modal fade" id="sharedFolderModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">List of users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('shared-content.destroy')}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <input type="hidden" value="{{$folder->id}}" name="folder_id">
                            <select name="user_id" class="form-control">

                                @foreach($shared_users as $user)
                                    <option value="{{$user->user_id}}">{{$user->user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button  class="btn btn-danger" >Remove user</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

{{--        delete folder--}}
        <div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('folder.destroy', ['folder' => $folder])}}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button  class="btn btn-danger" >Do you really want to delete this folder?</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additional_scripts')
    <script>
        $(document).ready(function() {
            $('#shared_user_id').select2();
        });
    </script>
@endsection

