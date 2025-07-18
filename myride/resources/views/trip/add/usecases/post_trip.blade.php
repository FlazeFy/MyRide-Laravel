<style>
    #map-board {
        height:50vh;
        border-radius: 20px;
        margin-bottom: 6px;
        border: 5px solid black;
    }

    /* Maps Dialog */
    .gm-ui-hover-effect {
        background: black !important;
        border-radius: 100%;
        position: absolute !important;
        right: 6px !important;
        top: 6px !important;
    }
    .gm-ui-hover-effect span {
        color: white !important;
    }
    .gm-control-active {
        background: black !important;
        border: 1.75px solid white !important;
        border-radius: 10px !important;
        margin-bottom: 10px !important;
    }
    .gmnoprint div{
        background: transparent !important;
        box-shadow: none !important;
    }
    .gm-control-active span {
        background: white !important;
    }
</style>

<div class="mx-4">
    <h2>Add Trip</h2>
    <form id="form-add-trip">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Vehicle Name</label>
                        <select class="form-select" name="vehicle_id" id="vehicle_id" aria-label="Default select example">
                            @foreach($dt_all_vehicle as $dt)
                                <option value="{{$dt->id}}">{{$dt->vehicle_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Trip Category</label>
                        <select class="form-select" name="trip_category" id="trip_category" aria-label="Default select example">
                            @foreach($dt_trip_category as $dt)
                                <option value="{{$dt->dictionary_name}}">{{$dt->dictionary_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Description</label>
                        <textarea class="form-control" name="trip_desc" id="trip_desc" required></textarea>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Person</label>
                        <textarea class="form-control" name="trip_person" id="trip_person" required></textarea>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Trip Origin Name</label>
                        <input class="form-control" name="trip_origin_name" id="trip_origin_name" requried>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Trip Destination Name</label>
                        <input class="form-control" name="trip_destination_name" id="trip_destination_name" requried>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Trip Origin Coordinate</label>
                        <input class="form-control" name="trip_origin_coordinate" id="trip_origin_coordinate" requried>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label>Trip Destination Coordinate</label>
                        <input class="form-control" name="trip_destination_coordinate" id="trip_destination_coordinate" requried>
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
            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="position-relative">
                    <div id="map-board"></div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    let map
    let markerCounter = 0

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
            document.getElementById('trip_origin_coordinate').value = coor['lat'] + ', ' + coor['lng']
        } else if(markerCounter == 1){
            document.getElementById('trip_destination_coordinate').value = coor['lat'] + ', ' + coor['lng']
        }
    }

    function resetMarker() {
        location.reload()
    }

    window.initMap = initMap;

    $(document).on('click','#submit-add-trip-btn', function(){
        post_trip()
    })

    const post_trip = () => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/trip`,
            type: 'POST',
            data: $('#form-add-trip').serialize(),
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
                        window.location.href = '/trip'
                    }   
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generate_api_error(response, true)
            }
        });
    }
</script>