@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="container">
            @include('reminder.add.usecases.post_reminder')
        </div>
    </div>
@endsection