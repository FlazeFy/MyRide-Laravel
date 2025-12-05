<style>
    #map-board {
        height:40vh;
    }
</style>

<h2>Add Reminder</h2><hr>
<form id="form-add-reminder">
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
            @include('reminder.add.usecases.get_attached_reminder')
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <label>Reminder Context</label>
                    <select class="form-select" name="reminder_context" id="reminder_context_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-md-6 col-sm-12">
                    <label>Remind At</label>
                    <input class="form-control" type="datetime-local" name="remind_at" id="remind_at">
                </div>
            </div>
            <label>Reminder Title</label>
            <input class="form-control" name="reminder_title" id="reminder_title">
            <label>Reminder Body</label>
            <textarea class="form-control" name="reminder_body" id="reminder_body"></textarea>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="mb-0">Reminder Attachment</label>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary py-1" id="add_image-button"><i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span></a>
                    <a class="btn btn-primary py-1" id="add_location-button"><i class="fa-solid fa-map"></i><span class="d-none d-md-inline"> Add Map</span></a>
                </div>
            </div>
            <div id="reminder_image_attachment-holder"></div>
            <div id="reminder_location_attachment-holder"></div>
            <br>
            <div class="d-grid d-md-inline-block">
                <a class="btn btn-success rounded-pill p-3 w-100 w-md-auto mt-3" id="submit-add-reminder-btn"><i class="fa-solid fa-floppy-disk"></i> Save Reminder</a>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    let map
    let marker = null

    function initMap() {
        const board = document.getElementById("map-board")
        if (!board) return

        map = new google.maps.Map(board, {
            center: { lat: -6.226838579766097, lng: 106.82157923228753 },
            zoom: 12
        })

        map.addListener("click", (e) => {
            if (marker) marker.setMap(null)
            marker = new google.maps.Marker({ position: e.latLng, map: map })
            const c = e.latLng.toJSON()

            $('#reminder_coordinate').val(`${c.lat}, ${c.lng}`)
        })
    }

    window.initMap = initMap

    $(document).on('blur', '#reminder_coordinate', function () {
        const value = $(this).val().trim()
        if (!value) return

        const parts = value.split(',')
        if (parts.length !== 2) return

        const lat = parseFloat(parts[0])
        const lng = parseFloat(parts[1])

        if (isNaN(lat) || isNaN(lng)) return

        const position = { lat, lng }

        if (marker) marker.setMap(null)
        marker = new google.maps.Marker({ position, map })

        map.panTo(position)
        map.setZoom(14)
    })
    
    template_alert_container('reminder_attachment-holder', 'no-data', "No attachment selected", null, '<i class="fa-solid fa-link"></i>', null)
    $(document).on('click','#submit-add-reminder-btn', function(){
        post_reminder()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        get_vehicle_detail(id)
    })

    $(document).ready(function() {
        $(document).on('click','#add_image-button',function(){
            $('#reminder_image_attachment-holder').html(`
                <div class='container-fluid'>
                    <input type="file" id="image-input" accept="image/jpeg,image/png,image/gif"><br>
                    <img id="image-preview" class="mt-2 d-none" style="max-width: 200px;">
                </div>
            `)
        })
        $(document).on('click','#add_location-button',function(){
            $('#reminder_location_attachment-holder').html(`
                <div class='container-fluid'>
                    <div id="map-board" class="maps-toolbar"></div>
                    <label>Coordinate</label>
                    <input class="form-control" name="reminder_coordinate" id="reminder_coordinate" requried>
                </div>
            `)

            setTimeout(() => initMap(), 100)
        })

        $(document).on('change', '#image-input', function(e) {
            const file = e.target.files[0]
            if (!file) return

            const maxSize = 5 * 1024 * 1024

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'Maximum file size is 5 MB'
                })

                $(this).val('')
                $('#image-preview').addClass('d-none').attr('src', '')
                return
            }

            const reader = new FileReader()
            reader.onload = function (event) {
                $('#image-preview').attr('src', event.target.result).removeClass('d-none')
            }
            reader.readAsDataURL(file)
        })
    })

    get_vehicle_name_opt(token)
    get_context_opt('reminder_context',token)

    const post_reminder = () => {
        const vehicle_id = $('#vehicle_holder').val()
        const reminder_context = $('#reminder_context_holder').val()

        if(vehicle_id !== "-" && reminder_context !== "-"){
            Swal.showLoading();
            $.ajax({
                url: `/api/v1/reminder`,
                type: 'POST',
                contentType: "application/json",
                data: JSON.stringify({
                    vehicle_id: vehicle_id,
                    reminder_title: $('#reminder_title').val(),
                    reminder_context: reminder_context,
                    reminder_body: $("#reminder_body").val(),
                    remind_at: formatDateTimeAPI($('#remind_at').val()),
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
                        window.location.href = '/reminder'
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generate_api_error(response, true)
                }
            });
        } else {
            failedMsg('create reminder : you must select an item')
        }
    }
</script>