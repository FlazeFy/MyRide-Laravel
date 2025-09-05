@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="container">
                    @include('history.usecases.get_all_list_history')
                </div>
            </div>
        </div>
    </div>
@endsection