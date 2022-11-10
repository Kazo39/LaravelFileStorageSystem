@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" >
                    <div class="card-header">
                        {{$file->file_name}}

                    </div>

                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#renameFileModal">Rename</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteFileModal">Delete</button>
                            </div>
                            <div class="col-4">
                                <a class="btn btn-primary" href="{{route('file.download', ['file' => $file])}}">Download</a>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <form action="{{route('shared-content.store')}}" method="POST">
                                <div class="col-6 offset-3">

                                    @csrf
                                    <input type="hidden" name="content_id" value="{{$file->id}}">
                                    <input type="hidden" name="content_type" value="file">
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

        <div class="modal fade" id="deleteFileModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('file.destroy', ['file' => $file])}}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button  class="btn btn-danger" >Do you really want to delete this file?</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="renameFileModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Rename file</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('file.update', ['file' => $file])}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="text" name="name" value="{{$file->file_name}}" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button  class="btn btn-success" >Save</button>
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
