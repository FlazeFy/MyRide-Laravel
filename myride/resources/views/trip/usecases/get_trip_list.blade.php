<?php 
    use App\Helpers\Converter;
?>

@php
    $carouselId = 'carouselTrip';
@endphp

<div class="carousel-parent">
    <div id="{{$carouselId}}" class="carousel slide">
        <div class="carousel-indicators"></div>
        <div class="carousel-inner py-4"></div>
        <div id="carousel-holder"></div>
    </div>
</div>

<script type="text/javascript">
    let page = 1
    var markers = []
    var dt_all_trip_location = []
    let lastPageCarousel = null
    let nextPageUrlCarousel = null
    let isFetchingNextCarousel = false

    const get_all_trip = (page) => {
        return new Promise((resolve, reject) => {
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/trip?page=${page}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    const payload = response.data
                    const data = payload.data
                    lastPageCarousel = payload.last_page
                    nextPageUrlCarousel = payload.next_page_url

                    if (page === 1) {
                        dt_all_trip_location = data
                        markers = []
                        $('#trip-content-holder').empty()
                        buildLayoutTrip(payload)
                    } else {
                        appendLayoutTrip(payload)
                    }

                    data.forEach(dt => place_marker(dt))
                    initMap()
                    resolve()

                    if (data.length > 3) {
                        templateCarouselNavigation("carousel-nav-holder", "<?= $carouselId ?>")
                    }
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        reject(errorThrown)
                        generateApiError(response, true)
                    } else {
                        templateAlertContainer(`<?= $carouselId ?>`, 'no-data', "No trip found", 'add a trip', '<i class="fa-solid fa-car"></i>','/trip/add')
                    }
                }
            });
        });
    };
    get_all_trip(page)

    $(document).ready(function() {
        $('#carouselTrip').on('slid.bs.carousel', function (e) {
            const carousel = e.target
            const items = carousel.querySelectorAll('.carousel-item')
            const lastIndex = items.length - 1

            if(e.to === lastIndex && !isFetchingNextCarousel && nextPageUrlCarousel && page < lastPageCarousel){
                isFetchingNextCarousel = true
                page++

                get_all_trip(page).then(() => {
                    const instance = bootstrap.Carousel.getOrCreateInstance(carousel, {
                        interval: false,
                        ride: false,
                        wrap: false
                    })
                    instance.pause()
                    isFetchingNextCarousel = false
                })
            }
        })
    })
</script>