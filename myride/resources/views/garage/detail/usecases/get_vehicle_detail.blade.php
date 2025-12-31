<style>
    .carousel-indicators button {
        width: var(--spaceXLG) !important;
        height: var(--spaceMini) !important;
        border-radius: 100%;
        background: var(--blueColor) !important;
    }
    .carousel-indicators button.active {
        background: var(--secondaryBlueColor) !important;
    }
</style>

<h1 id="vehicle_name"></h1><hr>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="btn btn-primary rounded-pill px-3 py-1" id="vehicle_type"></span>
        <span class="btn btn-success rounded-pill px-3 py-1" id="vehicle_transmission"></span>
        <h6 id="vehicle_merk" class="mb-0"></h6>
    </div>
    <h6 id="vehicle_distance" class="mb-0"></h6>
</div>
<p id="vehicle_desc"></p>
<br>
<h3 class="mb-2">Additional Info</h3><hr>
<div class="row d-flex flex-wrap align-items-center">
    <div class="col-xl-3 col-md-4 col-sm-6 col-6 pb-3">
        <h6 class="mb-2">Plate Number</h6>
        <span id="vehicle_plate_number"></span>
    </div>
    <div class="col-xl-3 col-md-4 col-sm-6 col-6 pb-2">
        <h6 class="mb-0">Status</h6>
        <span id="vehicle_status" class="d-flex flex-wrap gap-1"></span>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 col-6 pb-2">
        <h6 class="mb-0">Category</h6>
        <span class="text-secondary" id="vehicle_category"></span>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 col-6 pb-2">
        <h6 class="mb-0">Color</h6>
        <span class="text-secondary" id="vehicle_color"></span>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 col-6 pb-2">
        <h6 class="mb-0">Capacity</h6>
        <span class="text-secondary" id="vehicle_capacity"></span>
    </div>
</div>
<br>
<h3 class="mb-2">Fuel Info</h3><hr>
<div class="row">
    <div class="col-xl-3 col-md-3 col-sm-6 col-4">
        <h5 class="mb-0">Capacity</h5>
        <h5 class="fw-bold" id="vehicle_fuel_capacity"></h5>
    </div>
    <div class="col-xl-4 col-md-5 col-sm-6 col-8">
        <h5 class="mb-0">Status</h5>
        <h5 class="fw-bold" id="vehicle_fuel_status"></h5>
    </div>
    <div class="col-xl-5 col-md-4 col-sm-6 col-12">
        <h5 class="mb-0">Default Fuel</h5>
        <h5 class="fw-bold" id="vehicle_default_fuel"></h5>
    </div>
</div>

<script>
    let page_trip = 1
    let page_wash = 1
    let lastPageCarousel = null
    let nextPageUrlCarousel = null
    let isFetchingNextCarousel = false
    
    const get_vehicle_by_id = (id,page_trip,page_wash,is_initial) => {
        return new Promise((resolve, reject) => {
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/vehicle/detail/full/${id}?page_trip=${page_trip}&page_wash=${page_wash}`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const detail = response.data.detail
                    const trip_data = response.data.trip
                    const wash_data = response.data.wash
                    const driver_data = response.data.driver
                        
                    $('#vehicle_name').html(`${detail.vehicle_year_made} | ${detail.vehicle_name}`)
                    $('#vehicle_merk').html(detail.vehicle_merk)
                    $('#vehicle_type').html(detail.vehicle_type)
                    $('#vehicle_transmission').html(detail.vehicle_transmission)
                    $('#vehicle_category').html(detail.vehicle_category)
                    $('#vehicle_plate_number').html(`<span class="plate-number m-0">${detail.vehicle_plate_number}</span>`)
                    $('#vehicle_color').html(detail.vehicle_color)
                    $('#vehicle_default_fuel').html(detail.vehicle_default_fuel)
                    $('#vehicle_fuel_status').html(detail.vehicle_fuel_status)
                    $('#vehicle_capacity').html(`${detail.vehicle_capacity} person`)
                    $('#vehicle_status').html(
                        `${detail.deleted_at ? `<span class="btn btn-danger rounded-pill px-2 py-1 m-0" style="font-size:var(--textXSM);">Deleted at <span class="date-holder">${getDateToContext(detail.deleted_at,'calendar')}</span></span>` :''}
                        <span class="btn btn-success rounded-pill px-2 py-1 m-0" style="font-size:var(--textMD);">${detail.vehicle_status}</span>`
                    )
                    $('#vehicle_distance').html(`${detail.vehicle_distance} Km`)
                    $('#vehicle_desc').html(detail.vehilce_desc ?? '<span class="fst-italic">- No Description Provided -</span>')
                    $('#vehicle_fuel_capacity').html(`${detail.vehicle_fuel_capacity} Ltr`)

                    detail.vehicle_img_url && $('#vehicle_img_url-holder').html(`
                        <div class="container-fluid">
                            <h2>Vehicle Image</h2><hr>
                            <img class="img img-fluid" alt="${detail.vehicle_img_url}" src="${detail.vehicle_img_url}">
                        </div>
                    `)
                    if(detail.vehicle_document){
                        let docPreviewHolder = ''

                        detail.vehicle_document.forEach((dt, i) => {
                            let preview = ""

                            if (dt.vehicle_document_type === "image") {
                                preview = `<img src="${dt.vehicle_document_url}" class="img img-fluid my-2"style="max-width: 200px;">`
                            } else if (dt.vehicle_document_type === "pdf") {
                                preview = `<iframe src="${dt.vehicle_document_url}" style="width: 200px; height: 200px;" class="my-2"></iframe>`
                            } 

                            docPreviewHolder += `
                                <div class="col-xl-6 col-lg-12 col-md-6 col-sm-12">
                                    <div class="container-fluid my-3 text-center">
                                        ${preview}
                                        <p class="mt-1 mb-0"><b>Caption:</b> ${dt.vehicle_document_caption ?? "-"}</p>
                                    </div>
                                </div>
                            `
                        });

                        $("#vehicle_document_url-holder").html(`
                            <div class="container-fluid">
                                <h2>Document</h2><hr>
                                <div class="row">${docPreviewHolder}</div>
                            </div>
                        `);
                    }

                    if(detail.vehicle_other_img_url){
                        if(detail.vehicle_other_img_url.length === 1){
                            $('#vehicle_img_collection_url-holder').html(`
                                <div class="container-fluid">
                                    <h2>Others Image</h2><hr>
                                    <img class="img img-fluid" alt="${detail.vehicle_other_img_url[0].vehicle_img_url}" src="${detail.vehicle_other_img_url[0].vehicle_img_url}">
                                </div>
                            `)
                        } else {
                            let carouselInner = ''
                            let carouselIndicator = ''

                            detail.vehicle_other_img_url.forEach((dt, idx) => {
                                carouselInner += `
                                    <div class="carousel-item px-2 ${idx === 0 ? 'active' :''}">
                                        <img src="${dt.vehicle_img_url}" alt="${dt.vehicle_img_url}" class="d-block w-100">
                                    </div>
                                `
                                carouselIndicator += `<button type="button" data-bs-target="#carousel_other_image" data-bs-slide-to="${idx}" class="active" aria-current="true" aria-label="Slide ${idx+1}"></button>`
                            });

                            $('#vehicle_img_collection_url-holder').html(`
                                <div class="container-fluid">
                                    <h2>Others Image</h2><hr>
                                    <div id="carousel_other_image" class="carousel slide position-relative mb-4" data-bs-ride="carousel">
                                        <div class="carousel-indicators position-absolute" style="bottom: -50px;">${carouselIndicator}</div>
                                        <div class="carousel-inner">${carouselInner}</div>
                                        <div class="carousel-button-holder">
                                            <button class="btn btn-primary carousel-control-prev ms-2" type="button" data-bs-target="#carousel_other_image" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="btn btn-primary carousel-control-next" type="button" data-bs-target="#carousel_other_image" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `)
                        }
                    }

                    if(detail.deleted_at){
                        $('#delete_vehicle_button-holder').html(`
                        <a class="btn btn-danger btn-delete" data-type-delete="hard" data-context="Vehicle" data-url="/api/v1/vehicle/destroy/<?= $id ?>">
                            <i class="fa-solid fa-fire"></i> 
                            <span class="d-none d-md-inline"> Permanentelly Delete</span>
                        </a>`)
                        $('#recover_vehicle_button-holder').html(`
                            <a class="btn btn-success btn-recover" data-context="Vehicle" data-url="/api/v1/vehicle/recover/<?= $id ?>">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span class="d-none d-md-inline"> Recover</span>
                        </a>`)
                    } else {
                        $('#delete_vehicle_button-holder').html(`
                            <a class="btn btn-danger btn-delete" data-type-delete="soft" data-context="Vehicle" data-url="/api/v1/vehicle/delete/<?= $id ?>">
                            <i class="fa-solid fa-trash"></i>
                            <span class="d-none d-md-inline"> Delete</span>
                        </a>`)
                    }

                    build_layout_wash(wash_data,get_vehicle_by_id,page_trip,id)
                    build_layout_driver(driver_data)

                    if(trip_data){
                        lastPageCarousel = trip_data.last_page
                        nextPageUrlCarousel = trip_data.next_page_url

                        if (page_trip === 1) {
                            buildLayoutTrip(trip_data,"<?= $carouselId ?>")
                        } else {
                            appendLayoutTrip(trip_data,"<?= $carouselId ?>")
                        }

                        if(trip_data.data.length > 3){
                            templateCarouselNavigation("carousel-nav-holder", "<?= $carouselId ?>")
                        }
                        pauseCarousel("<?= $carouselId ?>")
                        syncCarouselIndicator("<?= $carouselId ?>")
                    } else {
                        templateAlertContainer(`<?= $carouselId ?>`, 'no-data', "No trip found", 'add a trip', '<i class="fa-solid fa-car"></i>','/trip/add')
                    }

                    if(is_initial){
                        get_vehicle_monthly_trip_stats(<?= date('Y') ?>,id)
                        get_vehicle_summary_trip_by_id(id)
                    }

                    resolve()
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404){
                        reject(errorThrown)
                        generateApiError(response, true)
                    } else {
                        failedRoute('vehicle','/garage')
                    }
                }
            });
        })
    }
    get_vehicle_by_id("<?= $id ?>",page_trip,page_wash,true)

    $(document).ready(function() {
        $('#<?= $carouselId ?>').on('slid.bs.carousel', function (e) {
            const carousel = e.target
            const items = carousel.querySelectorAll('.carousel-item')
            const lastIndex = items.length - 1

            if(e.to === lastIndex && !isFetchingNextCarousel && nextPageUrlCarousel && page_trip < lastPageCarousel){
                isFetchingNextCarousel = true
                page_trip++

                get_vehicle_by_id("<?= $id ?>",page_trip,page_wash,false).then(() => {
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
    })
</script>