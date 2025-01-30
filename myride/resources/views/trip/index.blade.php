@extends('layouts.main_layout')

@section('content')
    <div class="position-relative">
        <button class="btn btn-nav-page" onclick="window.location.href='/'"><i class="fa-solid fa-house"></i> Back to Home</button>
        <button class="btn btn-nav-page bg-success" onclick="initMap()"><i class="fa-solid fa-refresh"></i> Show All Trip</button>
        <button class="btn btn-nav-page bg-success" onclick="window.location.href='/trip/add'"><i class="fa-solid fa-plus"></i> Add Trip</button>
        <div class="row mx-3">
            <div class="col-lg-8 col-md-7 col-sm-12">
                @include('trip.usecases.get_map_board')
            </div>
            <div class="col-lg-4 col-md-5 col-sm-12">
                @include('trip.usecases.get_trip_list')
            </div>
        </div>
    </div>

    <script>
        let page = 1
        var markers = []
        var dt_all_trip_location = []

        const get_all_trip = (page) => {
            return new Promise((resolve, reject) => {
                Swal.showLoading();
                $.ajax({
                    url: `/api/v1/trip`,
                    type: 'GET',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Accept", "application/json")
                        xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                    },
                    success: function(response) {
                        Swal.close()
                        const data = response.data.data
                        dt_all_trip_location = data
                        markers = []
                        $('#trip-content-holder').empty()

                        data.forEach((dt, idx) => {
                            place_marker(dt)
                            get_trip_list(dt)
                        });
                        initMap()
                        resolve()
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        Swal.close()
                        Swal.fire({
                            title: "Oops!",
                            text: "Something went wrong",
                            icon: "error"
                        });
                        reject(errorThrown)
                    }
                });
            });
        };
        get_all_trip(page)

    </script>
@endsection