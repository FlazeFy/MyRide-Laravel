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
    <h6>Status : <span id="vehicle_status" class="ms-2"></span></h6>
    <h6>Plate Number : <span class="ms-2" id="vehicle_plate_number"></span></h6>
    <h6>Category : <span class="text-secondary ms-2" id="vehicle_category"></span></h6>
    <h6>Color : <span class="text-secondary ms-2" id="vehicle_color"></span></h6>
    <h6>Capacity : <span class="text-secondary ms-2" id="vehicle_capacity"></span></h6>
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
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
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
                $('#vehicle_status').html(`<span class="btn btn-success rounded-pill px-2 py-1 me-2" style="font-size:var(--textMD);">${detail.vehicle_status}</span>`)
                $('#vehicle_distance').html(`${detail.vehicle_distance} Km`)
                $('#vehicle_desc').html(detail.vehilce_desc ?? '<span class="fst-italic">- No Description Provided -</span>')
                $('#vehicle_fuel_capacity').html(`${detail.vehicle_fuel_capacity} Ltr`)

                build_layout_trip(trip_data)
                build_layout_clean(clean_data)
                build_layout_driver(driver_data)

                if(trip_data.data.length > 3){
                    template_carousel_navigation("carousel-nav-holder", "<?= $carouselId ?>")
                }
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                Swal.fire({
                    title: "Oops!",
                    text: "Something went wrong",
                    icon: "error"
                });
            }
        });
    }
    get_vehicle_by_id(page,"<?= $id ?>")
</script>