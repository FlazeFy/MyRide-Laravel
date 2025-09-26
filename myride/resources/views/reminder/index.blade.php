@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <a class="btn btn-success" href='/reminder/add'><i class="fa-solid fa-plus"></i> Add Reminder</a>
        <div class="container mt-2">
            @include('reminder.usecases.get_all_list_reminder')
            @include('reminder.usecases.hard_delete_reminder')
        </div>
    </div>
@endsection