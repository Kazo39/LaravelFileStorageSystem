@extends('layouts.app')

@section('content')
    <div class="container" >
        <div class="row justify-content-center">
            @if($error_msg != '')
                <div class="row">
                    <div class="alert alert-danger text-center">
                        <span>{{$error_msg}}</span>
                    </div>
                </div>
            @endif
            <div class="col-md-8">
                <div class="card " >
                    <div class="card-header">
                        Your files
                        <a type="button" class="btn btn-outline-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addFileModal" >Add file</a>
                        <a type="button" class="btn btn-outline-primary btn-sm float-end me-2" data-bs-toggle="modal" data-bs-target="#folderModal" >Add folder</a>
                    </div>

                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">Name</div>
                            <div class="col-2">Size</div>
                            <div class="col-1">Type</div>
                            <div class="col-2">Date modified</div>
                        </div>

                           @foreach($files as $file)
                               <div class="row">
                                   <div class="col-6">
                                       <i class="fa-solid fa-file ms-3"></i><a class="ms-2 small"  role="button" href="{{route('file.show', ['file' => $file])}}" >{{$file->file_name}}</a>
                                   </div>
                                   <div class="col-2 small">{{$file->file_size_formatted.' '.$file->file_size_unit}}</div>
                                   <div class="col-1 small">{{$file->file_type}} File</div>
                                   <div class="col-2 small">{{$file->updated_at->format('d.m.Y H:i')}}</div>
                               </div>
                           @endforeach


                           @foreach($folders as $folder)
                               <div class="row">
                                   <div class="col-6" onclick="open_close_folder({{$folder->id}})">
                                       <i class="fa-solid fa-folder" id="folder_{{$folder->id}}_icon"></i>
                                       <span class="ps-2 small" role="button">{{$folder->name}}</span>
                                   </div>

                                   <div class="col-3 small"><span class="float-end">File Folder</span></div>
                                   <div class="col-2 small">{{$folder->updated_at->format('d.m.Y H:i')}}</div>
                                   <div class="col-1 text-center">
                                       <a href="{{route('folder.show', ['folder' => $folder])}}" >
                                           <i class="fa-solid fa-circle-plus " ></i>
                                       </a>
                                   </div>
                                   <div class="row ms-2" id="folder_{{$folder->id}}"></div>
                               </div>
                           @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Modal -->
    <div class="modal fade" id="addFileModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dodavanje fajla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('file.store')}}" method="POST" id="form1" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="file" name="files[]" multiple class="form-control">
                        <input type="hidden" name="folder_id" id="folder_id" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button  class="btn btn-primary" >Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Folder Modal -->
    <div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dodavanje foldera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('folder.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="text" name="name"  class="form-control">
                        <input type="hidden" name="folder_id" id="folder_id_folder" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button  class="btn btn-primary" >Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- File Modal -->

@endsection

@section('additional_scripts')
    <script>
        function open_close_folder(folder_id){
            let folder_status = document.getElementById('folder_' + folder_id+ '_icon').classList.contains('fa-folder');
            let folder_file = document.getElementById('folder_id');
            let folder = document.getElementById('folder_id_folder');
            if(folder_status){
                folder.value = folder_id;
                folder_file.value = folder_id;
                openFolder(folder_id);
            }else{
                folder.value = null;
                folder_file.value = null;
                closeFolder(folder_id);
            }
        }
        async function openFolder(folder_id){
            let response = await fetch('folder/children/'+folder_id);
            let childrens = await response.json();
            let div = document.getElementById('folder_' + folder_id);

            let divHTML = "";

            childrens.forEach((child) =>{
                if(child['name']){
                    //let date = new Date(child['updated_at'])
                    divHTML += `
                                 <div class="col-6" onclick="open_close_folder(${child['id']})">
                                       <i class="fa-solid fa-folder" id="folder_${child['id']}_icon"></i>
                                       <span class="small" role="button">${child['name']}</span>
                                   </div>

                                   <div class="col-3 small"><span class="float-end">File Folder</span></div>
                                   <div class="col-2 small">${child['date_modified']}</div>
                                   <div class="col-1 text-center">
                                       <a href="folder/${child['id']}">
                                           <i class="fa-solid fa-circle-plus " ></i>
                                       </a>
                                   </div>
                                   <div class="row ms-2" id="folder_${child['id']}"></div>
                          `
                }
                else{
                    console.log(child)
                    divHTML += `
                                    <div class="col-6">
                                       <i class="fa-solid fa-file "></i><a class="ps-2 small"  role="button" href="file/${child['id']}" >${child['file_name']}</a>
                                    </div>
                                    <div class="col-2 small ">${child['file_size_formatted']}  ${child['file_size_unit']}</div>
                                    <div class="col-1 small">${child['file_type']} File</div>
                                    <div class="col-2 small">${child['date_modified']}</div>
                           `
                }
            });

            div.innerHTML = divHTML;
            document.getElementById('folder_' + folder_id+ '_icon').classList.remove('fa-folder');
            document.getElementById('folder_' + folder_id+ '_icon').classList.add('fa-folder-open');
        }
        function closeFolder(folder_id){
            document.getElementById('folder_' + folder_id).innerHTML = "";
            document.getElementById('folder_' + folder_id+ '_icon').classList.add('fa-folder');
            document.getElementById('folder_' + folder_id+ '_icon').classList.remove('fa-folder-open');
        }
    </script>
@endsection
