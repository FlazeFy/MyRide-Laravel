@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="container">
            @include('reminder.usecases.get_all_list_reminder')
        </div>
    </div>
@endsection