@extends('layouts.app')

@section('additional-css')
    <style>
        .progress-bar-outer{
            height: 25px;
            background-color: #ccc;
            position: relative;
            border-radius: 7px;
        }
        .progress-bar-outer .progress-bar-inner{
            position: absolute;
            height: 100%;
            border-radius: 7px;
            background-color: rgb(125,44,255);
        }
        #play-animation{
            animation: progress-animation 1.5s forwards;
        }
        @keyframes progress-animation{
            0% {width: 0%;}
            100% {width: {{$used_memory_percentage}}%;}
        }
    </style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        @if(!$last_three_shared_files_folders->isEmpty())
            <div class="row">
                <div class="col-12">
                    <ul class="float-end list-group list-group-light">
                        <span class="font-monospace">Last 3 shared files/folders</span>
                        @foreach($last_three_shared_files_folders as $content)
                            <div class="col-12">
                                @if($content->file_id)
                                    <a class="list-group-item mt-1 rounded-3 list-group-item-success list-group-item-action" href="{{route('file.download', ['file' => $content->file->id])}}">{{$content->file->file_name}}</a>
                                @else
                                    <a class="list-group-item mt-1 rounded-3 list-group-item-success list-group-item-action" href="{{route('shared-content.index')}}">{{$content->folder->name}}</a>
                                @endif
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="col-md-8 mt-3">
            <div class="card text-center" >
                <div class="card-header">Used memory</div>
                <div class="card-body">
                    <div class="progress-bar-outer">
                        <div class="progress-bar-inner text-center"></div>
                    </div>
                    <div class="row">
                        <div class="col mt-1">
                            <span>You are using {{auth()->user()->used_memory_formatted}} MB of {{auth()->user()->maximum_memory_formatted}} GB </span>
                        </div>
                    </div>
                </div>
            </div>
            @if(!$files_by_extension == [])
                <div class="card mt-5 text-center">
                    <div class="card-header">Ration of files by extension</div>
                    <div class="card-body " id="myDiv">

                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('additional_scripts')
    <script>
        function fill_progress_bar(){
            const progressBar = document.querySelector('.progress-bar-inner');
            progressBar.setAttribute('id', 'play-animation');
        }

        function pie_plot(){
            let data = [{
                values : [@foreach($files_by_extension as $file)
                    {{$file['total_size']}},
                    @endforeach
                        @if($files_below_5_percentage != [])
                        {{$files_below_5_percentage_total_size}}
                        @endif
                ],

                labels : [@foreach($files_by_extension as $file)
                    '{{$file['extension']}}',
                    @endforeach
                        @if($files_below_5_percentage != [])
                        'Other'
                    @endif
                ],
                type: 'pie'
            }];

            let layout = {
                height: 500,
                width: 700
            };

            Plotly.newPlot('myDiv', data, layout);
        }
        window.onload = function (){
            fill_progress_bar()
            pie_plot()
        };


    </script>
@endsection
