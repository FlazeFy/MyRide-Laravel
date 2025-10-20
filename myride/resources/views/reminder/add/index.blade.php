@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>

    <div class="position-relative">
        <a class="btn btn-danger" href='/reminder'><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="container mt-2">
            @include('reminder.add.usecases.post_reminder')
        </div>
    </div>
@endsection