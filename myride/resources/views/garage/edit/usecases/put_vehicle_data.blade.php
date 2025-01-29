<form id="form-edit-detail">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Vehicle Name & Transmission</label>
            <div class="input-group mb-3">
                <input class="form-control" type="text" name="vehicle_name" id="vehicle_name" required>
                <select class="form-select" aria-label="Default select example" name="vehicle_transmission_code" id="vehicle_transmission_code">
                    <option value="MT">Manual</option>
                    <option value="AT">Automatic</option>
                    <option value="CVT">CVT</option>
                </select>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Vehicle Merk</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_merk" id="vehicle_merk"></select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Vehicle Type</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_type" id="vehicle_type"></select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
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
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Distance</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_distance" id="vehicle_distance" min="1" required>
                <span class="input-group-text">Km</span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Category</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_category" id="vehicle_category"></select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Status</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_status" id="vehicle_status"></select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Year Made</label>
            <input class="form-control" type="number" name="vehicle_year_made" id="vehicle_year_made" min="1000" max="date('Y')" required>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Plate</label>
            <input class="form-control" type="text" name="vehicle_plate_number" id="vehicle_plate_number" required>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Default Fuel</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_default_fuel" id="vehicle_default_fuel"></select>     
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Fuel Capacity</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_fuel_capacity" id="vehicle_fuel_capacity"  min="1" max="100" required>
                <span class="input-group-text">Liter</span>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Fuel Status</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_fuel_status" id="vehicle_fuel_status"></select>    
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Main Color</label>
            <select class="form-select" aria-label="Default select example" name="vehicle_color" id="vehicle_color"></select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <label>Passanger Capacity</label>
            <div class="input-group mb-3">
                <input class="form-control" type="number" name="vehicle_capacity" id="vehicle_capacity" min="1" max="100" required>
                <span class="input-group-text">Person</span>
            </div>
        </div>
    </div>
    <a class="btn btn-success rounded-pill px-3 py-2" id="submit-edit-detail-btn"><i class="fa-solid fa-floppy-disk"></i> Save Changes</a>
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
                xhr.setRequestHeader("Authorization", `Bearer <?= session()->get("token_key"); ?>`)    
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
                generate_api_error(response, true)
            }
        });
    }
</script>
