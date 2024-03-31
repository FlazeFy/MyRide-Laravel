<style>
    #map-board {
        height:70vh;
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
    <div class="row">
        <div class="col-lg-6">
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
        </div>
        <div class="col-lg-6">
            <div class="position-relative">
                <div id="map-board"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let map
    let selected_color = ''

    function initMap() {
        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: -6.226838579766097, lng: 106.82157923228753},
            zoom: 12,
        });

        map.addListener("click", (e) => {
            initMap()
            placeMarkerAndPanTo(e.latLng, map)
            addContentCoor(e.latLng)
        });
    }

    function placeMarkerAndPanTo(latLng, map) {
        new google.maps.Marker({
            position: latLng,
            map: map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(40, 40),
            }
        });
        map.panTo(latLng)
    }

    function addContentCoor(coor){
        coor = coor.toJSON()
        document.getElementById('trip_origin_coordinate').value = coor['lat'] + ', ' + coor['lng']
    }

    window.initMap = initMap;
</script>