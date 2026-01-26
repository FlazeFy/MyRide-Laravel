<form id="form-add-vehicle">
    <h2>Add Vehicle</h2>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            @include('garage.add.usecases.select_image_collection')
        </div>
        <div class="col-md-6 col-sm-12">
            @include('garage.add.usecases.select_image_main')
        </div>
        <div class="col-xl-4 col-lg-4 col-md-8 col-sm-7 col-12">
            <label>Vehicle Name</label>
            <input class="form-control" name="vehicle_name" id="vehicle_name" required>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-5 col-12">
            <label>Transmission</label>
            <select class="form-select" name="vehicle_transmission" id="vehicle_transmission_holder" aria-label="Default select example"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-7 col-12">
            <label>Merk</label>
            <input class="form-control" name="vehicle_merk" id="vehicle_merk">
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-5 col-12">
            <label>Type</label>
            <select class="form-select" name="vehicle_type" id="vehicle_type_holder" aria-label="Default select example"></select>
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
            <textarea class="form-control" name="vehicle_desc" id="vehicle_desc"></textarea>
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
            <select class="form-select" name="vehicle_category" id="vehicle_category_holder" aria-label="Default select example"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
            <label>Status</label>
            <select class="form-select" name="vehicle_status" id="vehicle_status_holder" aria-label="Default select example"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-6 col-6">
            <label>Year Made</label>
            <input class="form-control" name="vehicle_year_made" id="vehicle_year_made" type="number">
        </div>
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
            <label>Plate Number</label>
            <input class="form-control" name="vehicle_plate_number" id="vehicle_plate_number">
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12">
            <label>Default Fuel</label>
            <select class="form-select" name="vehicle_default_fuel" id="vehicle_default_fuel_holder" aria-label="Default select example"></select>
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
            <select class="form-select" name="vehicle_fuel_status" id="vehicle_fuel_status_holder" aria-label="Default select example"></select>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-6">
            <label>Color</label>
            <input class="form-control" name="vehicle_color" id="vehicle_color">
        </div>
        <div class="col-xl-3 col-lg-4 col-md-3 col-sm-6 col-6">
            <label>Passanger Capacity</label>
            <input class="form-control" name="vehicle_capacity" id="vehicle_capacity" type="number">
        </div>
    </div>
    <div class="d-grid d-md-inline-block">
        <a class="btn btn-success rounded-pill w-100 w-md-auto mt-3" id="submit-add-vehicle-btn"><i class="fa-solid fa-floppy-disk"></i> Save Vehicle</a>
    </div>
</form>

<script>
    $(document).on('click','#submit-add-vehicle-btn', function(){
        post_vehicle()
    })

    $(document).on('change','#vehicle_category_holder', function(){
        const val = $(this).val()
    })

    ;(async () => {
        await getDictionaryByContextOption('vehicle_type,vehicle_transmission,vehicle_status,vehicle_fuel_status,vehicle_category,vehicle_default_fuel',token)
    })()

    const post_vehicle = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const vehicle_category = $('#vehicle_category_holder').val()

        if (vehicle_id === "-" || vehicle_category === "-") {
            failedMsg('create vehicle : you must select an item')
            return
        }

        const fd = new FormData()

        fd.append("vehicle_name", $('#vehicle_name').val())
        fd.append("vehicle_category", $('#vehicle_category_holder').val())
        fd.append("vehicle_type", $('#vehicle_type_holder').val())
        fd.append("vehicle_transmission", $('#vehicle_transmission_holder').val())
        fd.append("vehicle_status", $('#vehicle_status_holder').val())
        fd.append("vehicle_default_fuel", $('#vehicle_default_fuel_holder').val())
        fd.append("vehicle_fuel_status", $('#vehicle_fuel_status_holder').val())
        fd.append("vehicle_merk", $('#vehicle_merk').val())
        fd.append("vehicle_desc", $('#vehicle_desc').val())
        fd.append("vehicle_distance", $('#vehicle_distance').val())
        fd.append("vehicle_price", $('#vehicle_price').val())
        fd.append("vehicle_fuel_capacity", $('#vehicle_fuel_capacity').val())
        fd.append("vehicle_capacity", $('#vehicle_capacity').val())
        fd.append("vehicle_plate_number", $('#vehicle_plate_number').val())
        fd.append("vehicle_year_made", $('#vehicle_year_made').val())
        fd.append("vehicle_color", $('#vehicle_color').val())

        const img = $("#vehicle_image")[0] ? $("#vehicle_image")[0].files[0] : null
        fd.append("vehicle_image", img ? img : null)

        let totalFiles = 0

        $(".vehicle_other_images").each(function() {
            const files = this.files
            if (!files.length) return

            totalFiles += files.length
            for (let i = 0; i < files.length; i++) {
                fd.append("vehicle_other_img_url[]", files[i])
            }
        })

        if (totalFiles > 10) {
            failedMsg("You can only upload up to 10 other images")
            return
        }

        $.ajax({
            url: `/api/v1/vehicle`,
            type: 'POST',
            processData: false,
            contentType: false,
            data: fd,
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function (response) {
                Swal.close()
                Swal.fire("Success!", response.message, "success").then(() => {
                    window.location.href = '/garage'
                })
            },
            error: function (response) {
                Swal.close()
                if (response.status === 500) {
                    generateApiError(response, true)
                } else {
                    failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                }
            }
        })
    }
</script>