@extends('layouts.main_layout')

@section('content')
    <script>
        const year = <?= session()->get('toogle_select_year') ?>;
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    <div class="py-3">
        <div class="row">
            <div class="col-xl-9 col-lg-8 col-md-12">
                <div class='container text-center'>
                    @include('dashboard.usecases.get_summary')
                </div>
                <div class='container'>
                    @include('dashboard.usecases.get_vehicle_readiness')
                    @include('dashboard.usecases.set_action_readiness')
                </div>
                <div class='container'>
                    <div class="row">
                        <div class="col-md-12">
                            @include('dashboard.usecases.set_filter_year')
                        </div>
                    </div>
                    @include('dashboard.usecases.get_total_trip_monthly')
                </div>
                <div class='container'>
                    <div class="row">
                        <div class="col-md-12">
                            @include('dashboard.usecases.set_filter_fuel_monthly')
                        </div>
                    </div>
                    @include('dashboard.usecases.get_total_fuel_monthly')
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-12">
                <div class="row">
                    <div class="col-lg-12 col-md-6">
                        <div class='container text-center'>
                            @include('dashboard.usecases.get_next_reminder')
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6">
                        <div class='container text-center'>
                            @include('dashboard.usecases.get_trip_discovered')
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6">
                        <div class='container text-center'>
                            @include('dashboard.usecases.get_last_trip')
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-6">
                        <div class='container text-center'>
                            @include('dashboard.usecases.get_next_service')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection