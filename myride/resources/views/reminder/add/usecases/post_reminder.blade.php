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
                    <input class="form-control form-validator" data-validator="must_future" type="datetime-local" name="remind_at" id="remind_at">
                </div>
            </div>
            <label>Reminder Title</label>
            <input class="form-control" name="reminder_title" id="reminder_title">
            <label>Reminder Body</label>
            <textarea class="form-control" name="reminder_body" id="reminder_body"></textarea>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="mb-0">Reminder Attachment</label>
                <div class="d-flex flex-wrap gap-2" id="reminder_attachment_button-holder">
                    <a class="btn btn-primary py-1" id="add_image-button"><i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span></a>
                    <a class="btn btn-primary py-1" id="add_location-button"><i class="fa-solid fa-map"></i><span class="d-none d-md-inline"> Add Map</span></a>
                </div>
            </div>
            <div id="reminder_attachment-holder"></div>
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

            $('#reminder_location').val(`${c.lat}, ${c.lng}`)
        })
    }

    window.initMap = initMap

    $(document).on('blur', '#reminder_location', function () {
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
    
    templateAlertContainer('reminder_attachment-holder', 'no-data', "No attachment selected", null, '<i class="fa-solid fa-link"></i>', null)
    $(document).on('click','#submit-add-reminder-btn', function(){
        post_reminder()
    })
    $(document).on('change','#vehicle_holder', function(){
        const id = $(this).val()
        getVehicleDetail(id)
    })

    const cleanAlertContainer = () => {
        $("#reminder_attachment-holder .alert-container").remove()
    }
    const addClearButton = () => {
        if($('#reminder_attachment_button-holder').find('#clear_attachment-button').length === 0){
            $('#reminder_attachment_button-holder').prepend(`
                <a class="btn btn-danger py-1" id="clear_attachment-button"><i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span></a>
            `)
        }
    }

    $(document).ready(function() {
        $(document).on('click', '#clear_attachment-button', function(){
            $('#reminder_attachment_button-holder').find(this).remove()
            templateAlertContainer('reminder_attachment-holder', 'no-data', "No attachment selected", null, '<i class="fa-solid fa-link"></i>', null)
        })

        $(document).on('click', '#add_image-button', function () {
            cleanAlertContainer()

            if ($("#reminder_attachment-holder .reminder-image-holder").length > 0) {
                Swal.fire("Error!", "You can only add one image as attachment", "error")
                return
            }
            addClearButton()

            $("#reminder_attachment-holder").append(`
                <div class="container-fluid reminder-image-holder mt-2">
                    <input type="file" id="reminder_image" class="form-control" accept="image/jpeg,image/png,image/gif"><br>
                    <img id="image-preview" class="mt-1 d-none" style="max-width: 200px;">
                </div>
            `)
        })

        $(document).on('click','#add_location-button',function(){
            cleanAlertContainer()

            if ($("#reminder_attachment-holder .reminder-location-holder").length > 0) {
                Swal.fire("Error!", "You can only add one location as attachment", "error")
                return
            }
            addClearButton()

            $("#reminder_attachment-holder").append(`
                <div class="container-fluid reminder-location-holder">
                    <div id="map-board" class="maps-toolbar"></div>
                    <label>Coordinate</label>
                    <input class="form-control" name="reminder_location" id="reminder_location" required>
                </div>
            `)

            setTimeout(() => initMap(), 100)
        })

        $(document).on('change', '#reminder_image', function(e) {
            const file = e.target.files[0]
            if (!file) return

            const maxSize = 5 * 1024 * 1024

            if (file.size > maxSize) {
                failedMsg('File too large. Maximum file size is 5 MB')

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

    ;(async () => {
        await getVehicleNameOption(token)
        await getDictionaryByContextOption('reminder_context',token)
    })()

    const post_reminder = () => {
        const remindAt = $('#remind_at').val()
        if (remindAt === "") {
            failedMsg('you must select specific date and time')
            return
        }

        const remindAtDate = new Date(remindAt)
        const now = new Date()
        if (remindAtDate <= now) {
            failedMsg('the reminder date & time must be in the future')
            return
        }

        const vehicle_id = $('#vehicle_holder').val()
        const reminder_context = $('#reminder_context_holder').val()

        if (vehicle_id === "-" || reminder_context === "-") {
            failedMsg('create reminder : you must select an item')
            return
        }

        const fd = new FormData()

        fd.append("vehicle_id", vehicle_id)
        fd.append("reminder_title", $('#reminder_title').val())
        fd.append("reminder_context", reminder_context)
        fd.append("reminder_body", $('#reminder_body').val())
        fd.append("remind_at", formatDateTimeAPI(remindAt))

        const hasImage = $("#reminder_attachment-holder .reminder-image-holder").length > 0
        const hasLocation = $("#reminder_attachment-holder .reminder-location-holder").length > 0

        if (hasImage) {
            const img = $("#reminder_image")[0].files[0]
            if (img) fd.append("reminder_image", img)
            else fd.append("reminder_image", null)
        } 

        if (hasLocation) {
            const coor = $("#reminder_location").val()
            fd.append("reminder_location", coor ? coor : null)
        } 

        Swal.showLoading()

        $.ajax({
            url: `/api/v1/reminder`,
            type: 'POST',
            processData: false,
            contentType: false,
            data: fd,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
            },
            success: function (response) {
                Swal.close()
                Swal.fire("Success!",response.message, "success").then(() => {
                    window.location.href = '/reminder'
                })
            },
            error: function (response) {
                generateApiError(response, true)
            }
        })
    }
</script>