<style>
    #map-board {
        height:80vh;
        border-radius: var(--roundedLG);
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
        background: var(--darkColor) !important;
        border: 1.75px solid var(--whiteColor) !important;
        border-radius: 10px !important;
        margin-bottom: 10px !important;
    }
    .gmnoprint div{
        background: transparent !important;
        box-shadow: none !important;
        position: absolute;
        top: -30px;
        right: -15px;
    }
    .gm-control-active span {
        background: var(--whiteColor) !important;
    }

    .maps-toolbar {
        border-radius: 20px;
        border: 5px solid var(--darkColor);
        padding: 0 !important;
        background: var(--darkColor);
    }
    .maps-toolbar button {
        margin: 10px !important;
        border-radius: 10px !important;
    }
    .maps-info-box p, .maps-info-box h6 {
        color: var(--textWhite) !important;
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

    window.initMap = initMap;
</script>