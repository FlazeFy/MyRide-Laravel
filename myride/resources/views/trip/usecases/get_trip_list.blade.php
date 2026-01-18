<?php 
    use App\Helpers\Converter;
?>

@php
    $carouselId = 'carouselTrip';
@endphp

<style>
    .carousel-item .holder button:last-child {
        margin-bottom: var(--spaceSM) !important;
    }
</style>

<div class="carousel-parent">
    <div id="{{$carouselId}}" class="carousel slide">
        <div class="carousel-inner pt-4"></div>
    </div>
</div>

<script type="text/javascript">
    let page = 1
    var markers = []
    var dt_all_trip_location = []
    let lastPageCarousel = null
    let nextPageUrlCarousel = null
    let isFetchingNextCarousel = false

    $(document).on('blur','.search-input', function(){
        const val = $(this).val().trim()
        getAllTrip(1,val !== "" ? val : null)
    })

    const getAllTrip = (page, search) => {
        return new Promise((resolve, reject) => {
            let queryTripId = ''

            $(document).ready(function () {
                const params = new URLSearchParams(window.location.search)
                const searchQuery = search ? `&search=${search}` : ''

                if (params.has("trip_id")) {
                    queryTripId = `&trip_id=${params.get("trip_id")}`
                }
           
                $.ajax({
                    url: `/api/v1/trip?page=${page}${queryTripId}${searchQuery}`,
                    type: 'GET',
                    beforeSend: function (xhr) {
                        Swal.showLoading()
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
                            buildLayoutTrip(payload,"<?= $carouselId ?>")
                        } else {
                            appendLayoutTrip(payload,"<?= $carouselId ?>")
                        }

                        data.forEach(dt => place_marker(dt))
                        initMap()
                        resolve()

                        if (data.length > 3) {
                            templateCarouselNavigation("carousel-nav-holder", "<?= $carouselId ?>")
                        }

                        pauseCarousel("<?= $carouselId ?>")
                        syncCarouselIndicator("<?= $carouselId ?>")
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
                })
            })
        })
    }
    getAllTrip(page, null)

    $(document).ready(function() {
        $('#<?= $carouselId ?>').on('slid.bs.carousel', function (e) {
            const carousel = e.target
            const items = carousel.querySelectorAll('.carousel-item')
            const lastIndex = items.length - 1

            if(e.to === lastIndex && !isFetchingNextCarousel && nextPageUrlCarousel && page < lastPageCarousel){
                isFetchingNextCarousel = true
                page++

                getAllTrip(page).then(() => {
                    pauseCarousel("<?= $carouselId ?>")
                    isFetchingNextCarousel = false
                })
            }
        })

        $(document).on('click', '.btn-page', function () {            
            navigateCarouselPageWithButtonPage(this,'<?= $carouselId ?>')
        })

        $(document).on('click', '.carousel-control-prev, .carousel-control-next', function () {
            const holder = $(this).data('bs-target').replace('#', '')
            const type = $(this).data('bs-slide')
            navigateCarouselPage(holder, type === 'next' ? 'next' : 'prev')
        })

        $(document).on('keydown', function (e) {
            if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return
            navigateCarouselPage('<?= $carouselId ?>', e.key === 'ArrowRight' ? 'next' : 'prev')
        })
    })
</script>