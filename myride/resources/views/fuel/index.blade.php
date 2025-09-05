@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <div class="row">
            <div class="col-lg-8 col-md-6 col-sm-12">
                <div class="container">
                    @include('fuel.usecases.get_all_list_fuel')
                </div>
            </div>
        </div>
    </div>
@endsection