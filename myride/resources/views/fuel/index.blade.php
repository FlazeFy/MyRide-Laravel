@extends('layouts.main_layout')

@section('content')
    <script src="{{ asset('/js/usecases/inventory_v1.0.js')}}"></script>
    <script src="{{ asset('/js/usecases/export_v1.0.js')}}"></script>
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="position-relative">
        <a class="btn btn-success" href='/fuel/add'><i class="fa-solid fa-plus"></i> Fuel</a>
        @include('fuel.usecases.get_export_fuel')
        <div class="row mt-2">
            <div class="col-lg-8 col-md-12">
                <div class="container">
                    @include('fuel.usecases.get_all_list_fuel')
                    @include('fuel.usecases.hard_delete_fuel')
                    @include('fuel.usecases.put_fuel')
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="row">
                    <div class="col-lg-12 col-md-6">
                        <div class="container">
                            @include('fuel.usecases.get_fuel_summary')
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6">
                        <div class="container">
                            @include('fuel.usecases.get_all_fuel_monitor')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection