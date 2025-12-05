<form id="form-edit-detail">
    <h2>Edit Vehicle</h2>
    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-8 col-sm-7 col-12">
            <label>Vehicle Name</label>
            <input class="form-control" type="text" name="vehicle_name" id="vehicle_name" required>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-5 col-12">
            <label>Transmission</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_transmission_code" id="vehicle_transmission_code">
                <option value="MT">Manual</option>
                <option value="AT">Automatic</option>
                <option value="CVT">CVT</option>
            </select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-7 col-12">
            <label>Merk</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_merk" id="vehicle_merk"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-5 col-12">
            <label>Type</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_type" id="vehicle_type"></select>
        </div>
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <label>Price</label>
            <div class="input-group mb-3">
                <span class="input-group-text">Rp. </span>
                <input class="form-control" type="number" name="vehicle_price" id="vehicle_price" min="1" required>
                <span class="input-group-text">.00</span>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <label>Description</label>
            <textarea class="form-control" rows="4" name="vehicle_desc" id="vehicle_desc" required></textarea>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">
            <label>Distance</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_distance" id="vehicle_distance" min="1" required>
                <span class="input-group-text">Km</span>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
            <label>Category</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_category" id="vehicle_category"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
            <label>Status</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_status" id="vehicle_status"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-6 col-6">
            <label>Year Made</label>
            <input class="form-control" type="number" name="vehicle_year_made" id="vehicle_year_made" min="1000" max="date('Y')" required>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
            <label>Plate Number</label>
            <input class="form-control" type="text" name="vehicle_plate_number" id="vehicle_plate_number" required>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12">
            <label>Default Fuel</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_default_fuel" id="vehicle_default_fuel"></select>     
        </div>
        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-6 col-6">
            <label>Fuel Capacity</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_fuel_capacity" id="vehicle_fuel_capacity"  min="1" max="100" required>
                <span class="input-group-text">Liter</span>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
            <label>Fuel Status</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_fuel_status" id="vehicle_fuel_status"></select>    
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-6">
            <label>Main Color</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_color" id="vehicle_color"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-6 col-6">
            <label>Passanger Capacity</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_capacity" id="vehicle_capacity" min="1" max="100" required>
                <span class="input-group-text">Person</span>
            </div>
        </div>
    </div>
    <div class="d-grid d-md-inline-block">
        <a class="btn btn-success rounded-pill w-100 w-md-auto" id="submit-edit-detail-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</a>
    </div>
</form>

<script>
    $(document).on('click','#submit-edit-detail-btn', function(){
        update_vehicle_detail('<?= $id ?>')
    })

    const update_vehicle_detail = (id) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/detail/${id}`,
            type: 'PUT',
            data: $('#form-edit-detail').serialize(),
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
            },
            success: function(response) {
                Swal.hideLoading()
                Swal.fire({
                    title: "Success!",
                    text: `${response.message}`,
                    icon: "success",
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        get_vehicle(id)
                    }   
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status !== 404){
                    generate_api_error(response, true)
                } else {
                    failedRoute('vehicle','/garage')
                }
            }
        });
    }
</script>
