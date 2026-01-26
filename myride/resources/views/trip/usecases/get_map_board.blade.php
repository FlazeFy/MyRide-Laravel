<style>
    #map-board {
        height:75vh;
    }
    @media (max-width: 767px) {
        #map-board {
            height:35vh;
        }
        #map-board-mobile {
            width: 96vw;
            position: fixed !important;
            top: 85px;
            z-index: 99;
        }
    }
    @media (min-width: 580px) and (max-width: 767px) {
        #map-board {
            height:40vh;
        }
        #map-board-mobile {
            width: 95vw;
            top: 110px !important;
        }
    }
</style>

<div id="map-board-mobile">
    <div id="map-board" class="maps-toolbar d-block"></div>
    <div class="position-relative d-block d-md-none">
        <a title="Expand Map" id="toggle_close-map" class="btn btn-danger mt-2 rounded-circle px-3 position-absolute" style="right:0"><i class="fa-solid fa-circle-xmark"></i></a>
    </div>
</div>

<script type="text/javascript">
    let map
    
    $(document).ready(() => {
        if($(window).width() < 767){
            $('#map-board').css('box-shadow','rgba(0, 0, 0, 0.35) 0px 5px 15px')
        }
        $(document).on('click','#toggle_close-map',function(){
            $(this).toggleClass('btn-danger mt-2 btn-primary mt-0')
            $(this).find('i').toggleClass('fa-circle-xmark fa-map')
            $('#map-board').toggleClass('d-block d-none')
            if($('#map-board').hasClass('d-block')){
                $('#map-board').css('box-shadow','rgba(0, 0, 0, 0.35) 0px 5px 15px')
            } else {
                $('#map-board').css('box-shadow','none')
            }
        })
        $(window).on('resize', function() {
            if($(window).width() < 767){
                $('#map-board').css('box-shadow','rgba(0, 0, 0, 0.35) 0px 5px 15px')
                if($('#toggle_close-map').find('i').hasClass('fa-map') && $('#map-board').hasClass('d-none')){
                    $('#toggle_close-map').find('i').toggleClass('fa-circle-xmark fa-map')
                    $('#toggle_close-map').toggleClass('btn-danger mt-2 btn-primary mt-0')
                }
            } else {
                if($('#map-board').hasClass('d-none')){
                    $('#map-board').toggleClass('d-none d-block')
                }
                $('#map-board').css('box-shadow','none')
            }
        })
    })

    const markerInfoHTML = (trip_desc, trip_category, created_at, placeLabel, placeName, originCoord, destinationCoord, vehicle_type) => {
        return `
            <div class="d-flex justify-content-between mb-2">
                <button class="btn btn-danger custom-close-btn py-1" style="font-size: var(--textMD); margin:0 !important;"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <span class="btn btn-primary rounded-pill px-3 py-1 text-capitalize" style="font-size: var(--textMD)px">${trip_category}</span>
            </div><hr>
            <h6 class="mb-1">${trip_desc}</h6>
            <p class="mt-2 mb-0 fw-bold">${placeLabel}</p>
            <p>${placeName}</p>
            <p class="mt-2 mb-0 fw-bold">Created At</p>
            <p class="mb-0">${created_at}</p>
            <a class="btn btn-success py-1 mt-2 btn-set-route" data-trip-origin-coordinate="${originCoord}" data-trip-destination-coordinate="${destinationCoord}" data-vehicle-type="${vehicle_type}" style="font-size: var(--textMD)px">
                <i class="fa-solid fa-map-pin"></i> Set Route
            </a>
        `
    }

    const place_marker = (dt) => {
        const coorOrigin = dt.trip_origin_coordinate.split(", ").map(Number)
        const coorDestination = dt.trip_destination_coordinate.split(", ").map(Number)

        markers.push(
            {
                coords: { lat: coorOrigin[0], lng: coorOrigin[1] },
                content: markerInfoHTML(
                    dt.trip_desc, dt.trip_category, dt.created_at, "Origin", dt.trip_origin_name, dt.trip_origin_coordinate, `${coorOrigin[0]},${coorOrigin[1]}`, dt.vehicle_type
                )
            },
            {
                coords: { lat: coorDestination[0], lng: coorDestination[1] },
                content: markerInfoHTML(
                    dt.trip_desc, dt.trip_category, dt.created_at, "Destination", dt.trip_origin_name, dt.trip_origin_coordinate, `${coorDestination[0]},${coorDestination[1]}`, dt.vehicle_type
                )
            }
        )
    }

    function add_marker(props){
        var marker = new google.maps.Marker({
            position: props.coords,
            map: map,
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png',
                scaledSize: new google.maps.Size(40, 40),
            }
        })

        if(props.iconImage){
            marker.setIcon(props.iconImage)
        }
        if(props.content){
            var infoWindow = new google.maps.InfoWindow({
                content: props.content
            })
            marker.addListener('click', function(){
                infoWindow.open(map, marker)
            })
            google.maps.event.addListener(infoWindow, 'domready', () => {
                document.querySelector('.custom-close-btn')?.addEventListener('click', () => {
                    infoWindow.close()
                })
            })
        }

        markers.push(marker)
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: -6.226838579766097, lng: 106.82157923228753},
            zoom: 12,
        })
        if (markers.length > 0) {
            markers.forEach(markerData => add_marker(markerData))
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
        })
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
        })
        map.panTo(latLong)
    }

    window.initMap = initMap
</script>