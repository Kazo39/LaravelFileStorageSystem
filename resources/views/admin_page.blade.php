@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 mt-3">
                <div class="card text-center" >
                    <div class="card-header">All users</div>
                    <div class="card-body">
                        <div class="row">
                                <div class="col-6">
                                    Name
                                </div>
                                <div class="col-5">
                                    Memory allowed
                                </div>
                                <div class="col-1">
                                    Edit
                                </div>

                        </div>
                        @foreach($users as $user)
                            <div class="row">
                                <div class="col-6">
                                    {{$user->name}}
                                </div>
                                <div class="col-5">
                                    {{$user->maximum_memory_formatted}} GB
                                </div>
                                <div class="col-1">
                                    <i class="fa-solid fa-pen" role="button" onclick="passValueToModal({{$user->id}})" data-bs-toggle="modal" data-bs-target="#userUpdateMemoryModal" ></i>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="userUpdateMemoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update user memory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('admin-page.update')}}">
                    <div class="modal-body">

                        <input type="hidden" id="user_id" name="user_id">
                        <input type="number" placeholder="Enter new memory value in GB" name="new_memory"  class="form-control">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button  class="btn btn-primary">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additional_scripts')
    <script>
        function passValueToModal(userId){
            document.getElementById('user_id').value = userId;
        }
    </script>
@endsection
