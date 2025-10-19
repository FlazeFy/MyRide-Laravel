<style>
    #map-board {
        height:80vh;
        border-radius: var(--roundedLG);
    }
</style>

<div class="maps-toolbar">
    <div class="d-flex justify-content-end">
        
    </div>
    <div class="position-relative">
        <div id="map-board"></div>
    </div>
</div>

<script type="text/javascript">
    let map;

    const place_marker = (dt) => {
        const coorOrigin = dt.trip_origin_coordinate.split(", ").map(Number)
        const coorDestination = dt.trip_destination_coordinate.split(", ").map(Number)

        markers.push(
            {
                coords: { lat: coorOrigin[0], lng: coorOrigin[1] },
                content: `<div class='maps-info-box'>
                    <h6>${dt.trip_desc}</h6>
                    <span class='bg-dark rounded-pill px-2 py-1 text-white'>${dt.trip_category}</span>
                    ${dt.trip_origin_name ? `<p class='mt-2 mb-0 fw-bold'>Origin</p><p>${dt.trip_origin_name}</p>` : ""}
                    <p class='mt-2 mb-0 fw-bold'>Created At</p>
                    <p>${dt.created_at}</p>
                    <a class='btn btn-dark rounded-pill px-2 py-1' style='font-size:12px;'>
                        <i class='fa-solid fa-location-arrow'></i> Set Direction
                    </a>
                </div>`
            },
            {
                coords: { lat: coorDestination[0], lng: coorDestination[1] },
                content: `<div class='maps-info-box'>
                    <h6>${dt.trip_desc}</h6>
                    <span class='bg-dark rounded-pill px-2 py-1 text-white'>${dt.trip_category}</span>
                    ${dt.trip_destination_name ? `<p class='mt-2 mb-0 fw-bold'>Destination</p><p>${dt.trip_destination_name}</p>` : ""}
                    <p class='mt-2 mb-0 fw-bold'>Created At</p>
                    <p>${dt.created_at}</p>
                    <a class='btn btn-dark rounded-pill px-2 py-1' style='font-size:12px;'>
                        <i class='fa-solid fa-location-arrow'></i> Set Direction
                    </a>
                </div>`
            }
        );
    }

    function add_marker(props){
        var marker = new google.maps.Marker({
            position: props.coords,
            map: map,
            icon: props.icon
        });

        if(props.iconImage){
            marker.setIcon(props.iconImage)
        }
        if(props.content){
            var infoWindow = new google.maps.InfoWindow({
                content: props.content
            });
            marker.addListener('click', function(){
                infoWindow.open(map, marker)
            });
        }

        markers.push(marker)
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: -6.226838579766097, lng: 106.82157923228753},
            zoom: 12,
        });

        if (markers.length > 0) {
            markers.forEach(markerData => add_marker(markerData));
        }
    }


    function show_location(lat1, long1, lat2, long2){
        refresh_map(lat1, long1)

        place_marker_and_pan_to(lat1, long1, map)
        place_marker_and_pan_to(lat2, long2, map)
    }

    function refresh_map(lat, long) {
        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: lat, lng: long},
            zoom: 12,
        });
    }

    function place_marker_and_pan_to(lat, long, map) {
        const latLong = { lat: lat, lng: long}

        new google.maps.Marker({
            position: latLong,
            map: map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png',
                scaledSize: new google.maps.Size(40, 40),
            }
        });
        map.panTo(latLong)
    }

    window.initMap = initMap;
</script>