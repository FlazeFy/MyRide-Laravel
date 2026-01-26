<h2>Add Service</h2><hr>
<form id="form-add-service">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Type</label>
                    <input class="form-control" name="vehicle_type" id="vehicle_type" readonly>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Category</label>
                    <input class="form-control" name="vehicle_category" id="vehicle_category" readonly>
                </div>
            </div>
            <hr>
            @include('service.add.usecases.get_service_history')
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Service Category</label>
                    <select class="form-select" name="service_category" id="service_category_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Total Price</label>
                    <input class="form-control form-validator" data-validator="must_positive" name="service_price_total" id="service_price_total" type="number" min="0">
                </div>
            </div>
            <label>Service Location</label>
            <input class="form-control" name="service_location" id="service_location">
            <div class="row">
                <div class="col">
                    <label>Created At</label>
                    <input class="form-control" type="datetime-local" name="created_at" id="created_at">
                </div>
                <div class="col">
                    <label>Remind At</label>
                    <input class="form-control form-validator" data-validator="must_future" type="datetime-local" name="remind_at" id="remind_at">
                </div>
            </div>
            
            <label>Service Notes</label>
            <textarea class="form-control" name="service_note" id="service_note" style="min-height:120px"></textarea>
            <hr>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-service-btn"><i class="fa-solid fa-floppy-disk"></i> Save Service</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-service-btn', function(){
        post_service()
    })

    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        getVehicleDetail(id)
        get_vehicle_service_history(id)
    })

    $(document).on('change','#service_category_holder', function(){
        const val = $(this).val()
    })

    ;(async () => {
        setCurrentLocalDateTime('created_at')
        await getVehicleNameOption(token)
        await getDictionaryByContextOption('service_category,service_type',token)
    })()

    const post_service = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const service_category = $('#service_category_holder').val()

        if(vehicle_id !== "-" && service_category !== "-"){
            Swal.showLoading()
            $.ajax({
                url: `/api/v1/service`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    service_note: $("#service_note").val(),
                    service_category: $("#service_category_holder").val(),
                    service_location: $("#service_location").val(),
                    service_price_total: $("#service_price_total").val(),
                    remind_at: $("#remind_at").val() ? formatDateTimeAPI($("#remind_at").val()) : null,
                    created_at: formatDateTimeAPI($("#created_at").val())
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    Swal.fire("Success!", response.message, "success").then(() => {
                        window.location.href = '/service'
                    })
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generateApiError(response, true)
                }
            })
        } else {
            failedMsg('create service : you must select an item')
        }
    }
</script>