@extends('layouts.app')

@section('content')
    <div class="container" >
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card " >
                    <div class="card-header">
                        Shared Content
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
                                    <i class="fa-solid fa-file ms-3"></i><a class="ms-2 small"  role="button" href="{{route('file.download', ['file' => $file->file])}}" >{{$file->file->file_name}}</a>
                                </div>
                                <div class="col-2 small">{{$file->file->file_size_formatted.' '.$file->file->file_size_unit}}</div>
                                <div class="col-1 small">{{$file->file->file_type}} File</div>
                                <div class="col-2 small">{{$file->file->updated_at->format('d.m.Y H:i')}}</div>
                            </div>
                        @endforeach

                        @foreach($folders as $folder)
                            <div class="row">
                                <div class="col-6" onclick="open_close_folder({{$folder->folder->id}})">
                                    <i class="fa-solid fa-folder" id="folder_{{$folder->folder->id}}_icon"></i>
                                    <span class="ms-2 small" role="button">{{$folder->folder->name}}</span>
                                </div>

                                <div class="col-3 small"><span class="float-end">File Folder</span></div>
                                <div class="col-2 small">{{$folder->folder->updated_at->format('d.m.Y H:i')}}</div>
                                <div class="col-1">

                                </div>
                                <div class=" row ms-2" id="folder_{{$folder->folder->id}}"></div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('additional_scripts')
    <script>
        function open_close_folder(folder_id){
            let folder_status = document.getElementById('folder_' + folder_id+ '_icon').classList.contains('fa-folder');
            //let folder_file = document.getElementById('folder_id');
            //let folder = document.getElementById('folder_id_folder');
            if(folder_status){
                //folder.value = folder_id;
                //folder_file.value = folder_id;
                openFolder(folder_id);
            }else{
                //folder.value = null;
                //folder_file.value = null;
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
                    let date = new Date(child['updated_at'])
                    console.log();
                    divHTML += `
                                 <div class="col-6" onclick="open_close_folder(${child['id']})">
                                       <i class="fa-solid fa-folder" id="folder_${child['id']}_icon"></i>
                                       <span class="ps-2 small" role="button">${child['name']}</span>
                                   </div>

                                   <div class="col-3 small"><span class="float-end">File Folder</span></div>
                                   <div class="col-2 small">${child['date_modified']}</div>
                                   <div class=" row ms-2" id="folder_${child['id']}"></div>
                          `
                }
                else{
                    divHTML += `
                                    <div class="col-6">
                                       <i class="fa-solid fa-file "></i><a class="ps-2 small"  role="button" href="file/download/${child['id']}" >${child['file_name']}</a>
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
