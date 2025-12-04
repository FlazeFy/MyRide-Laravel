<style>
    .btn-trip-box {
        padding: var(--spaceLG) !important;
        margin-bottom: var(--spaceLG);
        width: 100%;
        text-align: left !important;
        border: 1.5px solid var(--whiteColor) !important;
        border-radius: var(--roundedMD) !important;
    }
    .btn-trip-box:hover {
        transform: scale(1.025);
    }
</style>

<?php 
    use App\Helpers\Converter;
?>

@php
    $carouselId = 'carouselTrip';
@endphp

<div class="carousel-parent">
    <div id="{{$carouselId}}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="15000">
        <div class="carousel-indicators"></div>
        <div class="carousel-inner py-4"></div>
        <div id="carousel-holder"></div>
    </div>
</div>

<script type="text/javascript">
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
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data.data
                    dt_all_trip_location = data
                    markers = []
                    $('#trip-content-holder').empty()

                    build_layout_trip(response.data)
                    data.forEach((dt, idx) => {
                        place_marker(dt)
                    });
                    initMap()
                    resolve()

                    if(data.length > 3){
                        template_carousel_navigation("carousel-nav-holder", "<?= $carouselId ?>")
                    }
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        reject(errorThrown)
                        generate_api_error(response, true)
                    } else {
                        template_alert_container(`<?= $carouselId ?>`, 'no-data', "No trip found", 'add a trip', '<i class="fa-solid fa-luggage"></i>','/trip/add')
                    }
                }
            });
        });
    };
    get_all_trip(page)
</script>