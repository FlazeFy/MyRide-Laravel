<h2>Add Clean</h2>
<form id="form-add-clean">
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
                    <input class="form-control" type="datetime-local" name="clean_start_time" id="clean_start_time">
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>End At</label>
                    <input class="form-control" type="datetime-local" name="clean_end_time" id="clean_end_time">
                </div>
            </div>
            <hr>
            @include('clean.add.usecases.get_vehicle_last_clean')
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <label>Clean By</label>
                    <select class="form-select" name="clean_by" id="clean_by_holder" aria-label="Default select example">
                        <option>-</option>
                    </select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Clean Price</label>
                    <input class="form-control" type="number" min="1" id="clean_price" name="clean_price">
                </div>
            </div>
            <label>Address</label>
            <textarea class="form-control" name="clean_address" id="clean_address" required></textarea>
            <label>Description</label>
            <textarea class="form-control" name="clean_desc" id="clean_desc" required></textarea>
            <label>Checklist</label>
            <div class="bordered rounded p-3">
                <div class="row" id="checklist-holder"></div>
            </div>
            <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-clean-btn"><i class="fa-solid fa-floppy-disk"></i> Save Clean</a>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).on('click','#submit-add-clean-btn', function(){
        post_clean()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
    })

    const set_checklist_holder = () => {
        const list_checklist = ['is_clean_body', 'is_clean_window', 'is_clean_dashboard', 'is_clean_tires', 'is_clean_trash', 'is_clean_engine', 'is_clean_seat', 'is_clean_carpet', 'is_clean_pillows', 'is_fill_window_cleaning_water', 'is_clean_hollow']

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

    get_vehicle_name_opt(token)
    get_context_opt('clean_by',token)

    const post_clean = () => {
        const vehicle_id = $('#vehicle_holder').val()

        if(vehicle_id !== "-" && $('#clean_by').val() !== "-"){
            const clean_end_time = $('#clean_end_time').val()

            Swal.showLoading();
            $.ajax({
                url: `/api/v1/clean`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    clean_desc: $('#clean_desc').val(),  
                    clean_by: $('#clean_by_holder').val(), 
                    clean_tools: null,  
                    is_clean_body: $('#is_clean_body').is(':checked'), 
                    is_clean_window: $('#is_clean_window').is(':checked'), 
                    is_clean_dashboard: $('#is_clean_dashboard').is(':checked'), 
                    is_clean_tires: $('#is_clean_tires').is(':checked'), 
                    is_clean_trash: $('#is_clean_trash').is(':checked'), 
                    is_clean_engine: $('#is_clean_engine').is(':checked'), 
                    is_clean_seat: $('#is_clean_seat').is(':checked'),
                    is_clean_carpet: $('#is_clean_carpet').is(':checked'),
                    is_clean_pillows: $('#is_clean_pillows').is(':checked'),
                    clean_address: $('#clean_address').val(), 
                    clean_price: $('#clean_price').val(), 
                    clean_start_time: formatDateTimeAPI($('#clean_start_time').val()), 
                    clean_end_time: clean_end_time ? formatDateTimeAPI(clean_end_time) : null, 
                    is_fill_window_cleaning_water: $('#is_fill_window_cleaning_water').is(':checked'), 
                    is_clean_hollow: $('#is_clean_hollow').is(':checked')
                }),
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                },
                success: function(response) {
                    Swal.close()
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.location.href = '/clean'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status === 500){
                        failedMsg('create clean')
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            });
        } else {
            failedMsg('create clean : you must select an item')
        }
    }
</script>