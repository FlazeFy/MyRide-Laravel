@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 col-sm-12">
                <div class="container">
                    @include('history.usecases.get_all_list_history')
                </div>
            </div>
        </div>
    </div>
@endsection