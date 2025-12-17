@extends('layouts.main_layout')

@section('content')
    <script>
        const token = `<?= session()->get("token_key"); ?>`
    </script>

    @include('stats.usecases.get_csv_export')
    <div class="row text-center" id="stats_comparison-holder">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="container-fluid">
                @include('stats.usecases.get_total_trip_by_category')
            </div>
        </div>
        @include('stats.usecases.get_total_vehicle_by_context')
    </div>
    <div class="row text-center" id="stats_bar-holder">
        @include('stats.usecases.get_total_trip_by_origindes')
    </div>
@endsection