<h2>Add wash</h2><hr>
<form id="form-add-wash">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name & Plate Number</label>
                    <select class="form-select" name="vehicle_holder" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Type</label>
                    <input class="form-control" name="vehicle_type" id="vehicle_type" readonly>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Category</label>
                    <input class="form-control" name="vehicle_category" id="vehicle_category" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <label>Start At</label>
                    <input class="form-control" type="datetime-local" name="wash_start_time" id="wash_start_time">
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>End At</label>
                    <input class="form-control" type="datetime-local" name="wash_end_time" id="wash_end_time">
                </div>
            </div>
            <hr>
            @include('wash.add.usecases.get_vehicle_last_wash')
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <label>wash By</label>
                    <select class="form-select" name="wash_by" id="wash_by_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>wash Price</label>
                    <input class="form-control" type="number" min="1" id="wash_price" name="wash_price">
                </div>
            </div>
            <label>Address</label>
            <textarea class="form-control" name="wash_address" id="wash_address" required></textarea>
            <label>Description</label>
            <textarea class="form-control" name="wash_desc" id="wash_desc" required></textarea>
            <label>Checklist</label>
            <div class="bordered rounded p-3">
                <div class="row" id="checklist-holder"></div>
            </div>
            <div class="d-grid d-md-inline-block">
                <a class="btn btn-success rounded-pill p-3 w-100 w-md-auto mt-3" id="submit-add-wash-btn"><i class="fa-solid fa-floppy-disk"></i> Save wash</a>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-wash-btn', function(){
        post_wash()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
    })

    const set_checklist_holder = () => {
        const list_checklist = ['is_wash_body', 'is_wash_window', 'is_wash_dashboard', 'is_wash_tires', 'is_wash_trash', 'is_wash_engine', 'is_wash_seat', 'is_wash_carpet', 'is_wash_pillows', 'is_fill_window_washing_water', 'is_wash_hollow']

        $('#checklist-holder').empty()
        list_checklist.forEach(dt => {
            $('#checklist-holder').append(`
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="${dt}">
                        <label class="form-check-label">${ucEachWord(dt.replaceAll('_',' ').replaceAll('is',''))}</label>
                    </div>
                </div>
            `)
        });
    }
    set_checklist_holder()

    ;(async () => {
        await get_vehicle_name_opt(token)
        await get_context_opt('wash_by',token)
    })()

    const post_wash = () => {
        const vehicle_id = $('#vehicle_holder').val()

        if(vehicle_id !== "-" && $('#wash_by').val() !== "-"){
            const wash_end_time = $('#wash_end_time').val()

            Swal.showLoading();
            $.ajax({
                url: `/api/v1/wash`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    wash_desc: $('#wash_desc').val(),  
                    wash_by: $('#wash_by_holder').val(), 
                    wash_tools: null,  
                    is_wash_body: $('#is_wash_body').is(':checked'), 
                    is_wash_window: $('#is_wash_window').is(':checked'), 
                    is_wash_dashboard: $('#is_wash_dashboard').is(':checked'), 
                    is_wash_tires: $('#is_wash_tires').is(':checked'), 
                    is_wash_trash: $('#is_wash_trash').is(':checked'), 
                    is_wash_engine: $('#is_wash_engine').is(':checked'), 
                    is_wash_seat: $('#is_wash_seat').is(':checked'),
                    is_wash_carpet: $('#is_wash_carpet').is(':checked'),
                    is_wash_pillows: $('#is_wash_pillows').is(':checked'),
                    wash_address: $('#wash_address').val(), 
                    wash_price: $('#wash_price').val(), 
                    wash_start_time: formatDateTimeAPI($('#wash_start_time').val()), 
                    wash_end_time: wash_end_time ? formatDateTimeAPI(wash_end_time) : null, 
                    is_fill_window_washing_water: $('#is_fill_window_washing_water').is(':checked'), 
                    is_wash_hollow: $('#is_wash_hollow').is(':checked')
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
                        window.location.href = '/wash'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generate_api_error(response, true)
                }
            });
        } else {
            failedMsg('create wash : you must select an item')
        }
    }
</script>