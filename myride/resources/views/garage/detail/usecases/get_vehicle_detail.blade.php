<div class="container">
    <h1 class="mb-1" id="vehicle_name"></h1>
    <div class="d-flex justify-content-between">
        <h4><span class="btn btn-success rounded-pill px-3 me-2" style="font-size:var(--textXMD);" id="vehicle_type"></span><span id="vehicle_merk"></span></h4>
        <h4 id="vehicle_distance"></h4>
    </div>
    <br>
    <p id="vehicle_desc"></p>
    <br>
    <h5 class="mb-4">Additional Info</h5><hr>
    <div class="row mb-3">
        <div class="col-md-6 col-sm-12">
            <h6 class="mb-2">Plate Number</h6>
            <span id="vehicle_plate_number"></span>
        </div>
        <div class="col-md-6 col-sm-12">
            <h6 class="mb-0">Status</h6>
            <span id="vehicle_status" class="ms-2"></span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <h6 class="mb-0">Category</h6>
            <span class="text-secondary" id="vehicle_category"></span>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <h6 class="mb-0">Color</h6>
            <span class="text-secondary" id="vehicle_color"></span>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <h6 class="mb-0">Capacity</h6>
            <span class="text-secondary" id="vehicle_capacity"></span>
        </div>
    </div>
</div>
<div class="container">
    <h5 class="mb-4">Fuel Info</h5><hr>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <h5>Capacity</h5>
            <h2 class="fw-bold" id="vehicle_fuel_capacity"></h2>
            <h5>Status</h5>
            <h2 class="fw-bold" id="vehicle_fuel_status"></h2>
        </div>
        <div class="col-md-6 col-sm-12 d-flex align-items-center">
            <div>
                <h5>Default Fuel</h5>
                <h2 class="fw-bold" id="vehicle_default_fuel"></h2>
            </div>
        </div>
    </div>
</div>

<script>
    let page = 1
    const get_vehicle_by_id = (page,id) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/detail/full/${id}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.close()
                const detail = response.data.detail
                const trip_data = response.data.trip
                const clean_data = response.data.clean
                const driver_data = response.data.driver
                    
                $('#vehicle_name').html(`${detail.vehicle_year_made} | ${detail.vehicle_name}`)
                $('#vehicle_merk').html(detail.vehicle_merk)
                $('#vehicle_type').html(detail.vehicle_type)
                $('#vehicle_category').html(detail.vehicle_category)
                $('#vehicle_plate_number').html(`<span class="plate-number mb-0">${detail.vehicle_plate_number}</span>`)
                $('#vehicle_color').html(detail.vehicle_color)
                $('#vehicle_default_fuel').html(detail.vehicle_default_fuel)
                $('#vehicle_fuel_status').html(detail.vehicle_fuel_status)
                $('#vehicle_capacity').html(`${detail.vehicle_capacity} person`)
                $('#vehicle_status').html(
                    `${detail.deleted_at ? `<span class="btn btn-danger rounded-pill px-2 py-1 me-2" style="font-size:var(--textMD);">Deleted at <span class="date-holder">${getDateToContext(detail.deleted_at,'calendar')}</span></span>` :''}
                    <span class="btn btn-success rounded-pill px-2 py-1 me-2" style="font-size:var(--textMD);">${detail.vehicle_status}</span>`
                )
                $('#vehicle_distance').html(`${detail.vehicle_distance} Km`)
                $('#vehicle_desc').html(detail.vehilce_desc ?? '<span class="fst-italic">- No Description Provided -</span>')
                $('#vehicle_fuel_capacity').html(`${detail.vehicle_fuel_capacity} Ltr`)

                trip_data ? build_layout_trip(trip_data) : template_alert_container(`<?= $carouselId ?>`, 'no-data', "No trip found", 'add a trip', '<i class="fa-solid fa-luggage"></i>','/trip/add')

                build_layout_clean(clean_data)
                build_layout_driver(driver_data)

                if(detail.deleted_at){
                    $('#delete_vehicle_button-holder').html(`<a class="btn btn-danger btn-delete" data-type-delete="hard" data-context="Vehicle" data-url="/api/v1/vehicle/destroy/<?= $id ?>"><i class="fa-solid fa-fire"></i> Permanentelly Delete</a>`)
                } else {
                    $('#delete_vehicle_button-holder').html(`<a class="btn btn-danger btn-delete" data-type-delete="soft" data-context="Vehicle" data-url="/api/v1/vehicle/delete/<?= $id ?>"><i class="fa-solid fa-trash"></i> Delete</a>`)
                }

                if(trip_data && trip_data.data.length > 3){
                    template_carousel_navigation("carousel-nav-holder", "<?= $carouselId ?>")
                }

                get_vehicle_monthly_trip_stats(<?= date('Y') ?>,"<?= $id ?>")
                get_vehicle_summary_trip_by_id("<?= $id ?>")
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    failedMsg(`get the vehicle`)
                } else {
                    failedRoute('vehicle','/garage')
                }
            }
        });
    }
    get_vehicle_by_id(page,"<?= $id ?>")
</script>