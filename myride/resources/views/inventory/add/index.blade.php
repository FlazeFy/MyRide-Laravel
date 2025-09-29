@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="container">
            @include('inventory.add.usecases.post_inventory')
        </div>
    </div>
@endsection