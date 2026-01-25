<style>
    #map-board {
        height:50vh;
    }
</style>

<h2>Add Trip</h2>
<hr>
<form id="form-add-trip">
    <div class="row">
        <div class="col-xl-6 col-lg-12 pb-4">
            <div class="position-relative">
                <div id="map-board" class="maps-toolbar"></div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="row">
                <div class="col-12">
                    <label>Vehicle Name</label>
                    <select class="form-select" name="vehicle_id" id="vehicle_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Category</label>
                    <select class="form-select" name="trip_category" id="trip_category_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Driver</label>
                    <select class="form-select" name="driver_id" id="driver_holder" aria-label="Default select example"></select>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Description</label>
                    <textarea class="form-control" name="trip_desc" id="trip_desc" required></textarea>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Person</label>
                    <textarea class="form-control form-validator" data-validator="tidy_up_comma" name="trip_person" id="trip_person" required></textarea>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Origin Name</label>
                    <input class="form-control" name="trip_origin_name" id="trip_origin_name" requried>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Destination Name</label>
                    <input class="form-control" name="trip_destination_name" id="trip_destination_name" requried>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0">Origin Coordinate</label>
                        <a class="btn btn-success py-1 btn-current-coordinate"><i class="fa-solid fa-map-pin"></i></a>
                    </div>
                    <input class="form-control form-validator" data-validator="must_coordinate" name="trip_origin_coordinate" id="trip_origin_coordinate" requried>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0">Destination Coordinate</label>
                        <a class="btn btn-success py-1 btn-current-coordinate"><i class="fa-solid fa-map-pin"></i></a>
                    </div>
                    <input class="form-control form-validator" data-validator="must_coordinate" name="trip_destination_coordinate" id="trip_destination_coordinate" requried>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <label>Departure At</label>
                    <input class="form-control" name="departure_at" id="departure_at" type="datetime-local">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <a class="btn btn-danger rounded-pill py-3 w-100 mt-3" onclick="resetMarker()"><i class="fa-solid fa-rotate-left"></i> Reset Location</a>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <a class="btn btn-success rounded-pill py-3 w-100 mt-3" id="submit-add-trip-btn"><i class="fa-solid fa-floppy-disk"></i> Save Trip</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    let map
    let markerCounter = 0

    setCurrentLocalDateTime("departure_at")

    function initMap() {
        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: -6.226838579766097, lng: 106.82157923228753 },
            zoom: 12,
        })

        map.addListener("click", (e) => {
            if(markerCounter < 2) {
                if(markerCounter == 0){
                    placeMarkerAndPanTo(e.latLng, map, 'Origin')
                } else if(markerCounter == 1){
                    placeMarkerAndPanTo(e.latLng, map, 'Destination')
                }
                addContentCoor(e.latLng)
                markerCounter++
            } 
        })
    }

    function placeMarkerAndPanTo(latLng, map, type) {
        new google.maps.Marker({
            position: latLng,
            map: map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(40, 40),
            },
            label: {
                text: type,
                color: 'white',
                fontSize: '14px', 
                fontWeight: '500'
            },
        })
        map.panTo(latLng)
    }

    function addContentCoor(coor) {
        coor = coor.toJSON()

        if(markerCounter == 0){
            $('#trip_origin_coordinate').val(coor['lat'] + ', ' + coor['lng'])
        } else if(markerCounter == 1){
            $('#trip_destination_coordinate').val(coor['lat'] + ', ' + coor['lng'])
        }
    }

    function resetMarker() {
        location.reload()
    }

    window.initMap = initMap

    $(document).on('click','#submit-add-trip-btn', function(){
        post_trip()
    })

    $(document).on('click','.btn-current-coordinate', function(){
        const $wrapper = $(this).closest('.col-lg-6, .col-md-6, .col-sm-12')
        const $input = $wrapper.find('input')

        if (!navigator.geolocation) {
            failedMsg("get current location. Geolocation is not supported by this browser")
            return
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude
                const lng = position.coords.longitude
                const coordinate = `${lat},${lng}`
                $input.val(coordinate).trigger('input')
            },
            function (error) {
                failedMsg("get current location")
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        )
    })

    const post_trip = () => {
        Swal.showLoading()

        if ($('#vehicle_holder').val() === "-" || $('#trip_category').val() === "-") {
            failedMsg('create trip : you must select an item')
            return
        }

        if ($('#trip_origin_name').val().trim() === $('#trip_destination_name').val().trim()){
            failedMsg('create trip : trip origin and destination name must be different')
            return
        }

        if ($('#trip_origin_coordinate').val().trim() === $('#trip_destination_coordinate').val().trim()){
            failedMsg('create trip : trip origin and destination coordinate must be different')
            return
        }

        $.ajax({
            url: `/api/v1/trip`,
            type: 'POST',
            data: $('#form-add-trip').serialize(),
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
                        window.location.href = '/trip'
                    }   
                })
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status === 500){
                    generateApiError(response, true)
                } else {
                    failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                }
            }
        })
    }

    ;(async () => {
        await getVehicleNameOption(token)
        getDriverNameOption(token)
        await getDictionaryByContextOption('trip_category',token)
    })()
</script>

@include('trip.add.usecases.get_trip_coordinate_by_location_name')
