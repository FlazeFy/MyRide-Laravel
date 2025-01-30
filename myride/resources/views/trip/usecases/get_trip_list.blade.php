<style>
    .btn-trip-box {
        color: var(--whiteColor) !important;
        padding: var(--spaceLG) !important;
        margin-bottom: var(--spaceLG);
        width: 100%;
        text-align: left !important;
        border: 1.5px solid var(--whiteColor) !important;
        border-radius: var(--roundedMD) !important;
    }
    .btn-trip-box:hover {
        transform: scale(1.05);
    }
</style>

<?php 
    use App\Helpers\Converter;
?>

<div id="trip-content-holder"></div>

<script type="text/javascript">
    const get_trip_list = (dt) => {
        const coorOrigin = dt.trip_origin_coordinate.split(", ").map(Number)
        const coorDestination = dt.trip_destination_coordinate.split(", ").map(Number)
        const deletedStyle = dt.deleted_at ? "background-color: rgba(221, 0, 33, 0.3);" : ""
        const deletedTitle = dt.deleted_at ? "title='Deleted Item'" : ""

        $('#trip-content-holder').append(`
            <button class="btn btn-trip-box" style="${deletedStyle}" ${deletedTitle} 
                onclick="show_location(${coorOrigin[0]}, ${coorOrigin[1]}, ${coorDestination[0]}, ${coorDestination[1]})">
                <h5 class="mb-4">${dt.trip_desc}</h5>
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Person With</h6>
                        ${dt.trip_person ? `<p class="mb-0">${dt.trip_person}</p>` : ""}
                    </div>
                    <div>
                        <h6 class="mb-0">Category</h6>
                        <p class="mb-0">${dt.trip_category}</p>
                    </div>
                </div>   
                <hr>
                <div class="mt-3 d-flex justify-content-between">
                    <div class="text-start">
                        <h6 class="mb-0">From</h6>
                        <p class="mb-0">${dt.trip_origin_name}</p>
                    </div>
                    <div class="text-center">
                        <a><i class="fa-solid fa-arrow-right"></i></a>
                        <p class="mb-0">${calculate_distance(coorOrigin[0], coorOrigin[1], coorDestination[0], coorDestination[1])} Km</p>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">Destination</h6>
                        <p class="mb-0">${dt.trip_destination_name}</p>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-start">
                    <div>
                        <h6 class="mb-0">Created At</h6>
                        <p class="mb-0">${dt.created_at}</p>
                    </div>
                    ${dt.updated_at ? `
                        <div>
                            <h6 class="mb-0">Updated At</h6>
                            <p class="mb-0">${dt.updated_at}</p>
                        </div>` : ""}
                    ${dt.deleted_at ? `
                        <div>
                            <h6 class="mb-0">Deleted At</h6>
                            <p class="mb-0">${dt.deleted_at}</p>
                        </div>` : ""}
                </div>
            </button>
        `)
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
</script>