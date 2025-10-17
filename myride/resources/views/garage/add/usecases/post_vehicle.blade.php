<h2>Add Vehicle</h2>
<form id="form-add-vehicle">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Category</label>
                    <select class="form-select" name="vehicle_category" id="vehicle_category_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Type</label>
                    <select class="form-select" name="vehicle_type" id="vehicle_type_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Transmission</label>
                    <select class="form-select" name="vehicle_transmission" id="vehicle_transmission_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Status</label>
                    <select class="form-select" name="vehicle_status" id="vehicle_status_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Fuel Status</label>
                    <select class="form-select" name="vehicle_fuel_status" id="vehicle_fuel_status_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Default Fuel</label>
                    <select class="form-select" name="vehicle_default_fuel" id="vehicle_default_fuel_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Merk</label>
                    <input class="form-control" name="vehicle_merk" id="vehicle_merk">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Year Made</label>
                    <input class="form-control" name="vehicle_year_made" id="vehicle_year_made" type="number">
                </div>
            </div>
            <label>Description</label>
            <textarea class="form-control" name="vehicle_desc" id="vehicle_desc" style="min-height:120px;"></textarea>
        </div>
        <div class="col-xl-6 col-lg-12">
            <label>Vehicle Name</label>
            <input class="form-control" name="vehicle_name" id="vehicle_name">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Travel Distance</label>
                    <input class="form-control" name="vehicle_distance" id="vehicle_distance" type="number">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Price</label>
                    <input class="form-control" name="vehicle_price" id="vehicle_price" type="number">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label>Fuel Capacity</label>
                    <input class="form-control" name="vehicle_fuel_capacity" id="vehicle_fuel_capacity" type="number">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <label>Capacity</label>
                    <input class="form-control" name="vehicle_capacity" id="vehicle_capacity" type="number">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Plate Number</label>
                    <input class="form-control" name="vehicle_plate_number" id="vehicle_plate_number">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Color</label>
                    <input class="form-control" name="vehicle_color" id="vehicle_color">
                </div>
            </div>
            <hr>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-vehicle-btn"><i class="fa-solid fa-floppy-disk"></i> Save Vehicle</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    const token = '<?= session()->get("token_key"); ?>'

    $(document).on('click','#submit-add-vehicle-btn', function(){
        post_vehicle()
    })
    $(document).on('change','#vehicle_category_holder', function(){
        const val = $(this).val()
    })

    get_context_opt('vehicle_type,vehicle_transmission,vehicle_status,vehicle_fuel_status,vehicle_category,vehicle_default_fuel',token)

    const post_vehicle = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const vehicle_category = $('#vehicle_category_holder').val()

        if(vehicle_id !== "-" && vehicle_category !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/vehicle`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_name: $('#vehicle_name').val(),
                    vehicle_category: $('#vehicle_category_holder').val(),
                    vehicle_type: $('#vehicle_type_holder').val(),
                    vehicle_transmission: $('#vehicle_transmission_holder').val(),
                    vehicle_status: $('#vehicle_status_holder').val(),
                    vehicle_default_fuel: $('#vehicle_default_fuel_holder').val(),
                    vehicle_fuel_status: $('#vehicle_fuel_status_holder').val(),
                    vehicle_merk: $('#vehicle_merk').val(),
                    vehicle_desc: $('#vehicle_desc').val(),
                    vehicle_distance: $('#vehicle_distance').val(),
                    vehicle_price: $('#vehicle_price').val(),
                    vehicle_fuel_capacity: $('#vehicle_fuel_capacity').val(),
                    vehicle_capacity: $('#vehicle_capacity').val(),
                    vehicle_plate_number: $('#vehicle_plate_number').val(),
                    vehicle_year_made: $('#vehicle_year_made').val(),
                    vehicle_color: $('#vehicle_color').val()
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.location.href = '/garage'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('create vehicle')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('create vehicle : you must select an item')
        }
    }
</script>