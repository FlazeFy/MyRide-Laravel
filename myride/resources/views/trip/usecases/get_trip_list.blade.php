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

<div>
    @foreach($dt_all_trip_location as $dt)
        @php($coor_destination = explode(", ", $dt->trip_destination_coordinate))
        @php($coor_origin = explode(", ", $dt->trip_origin_coordinate))
        <button class="btn btn-trip-box" <?php 
                if($dt->deleted_at != null){
                    echo "style='background-color: rgba(221, 0, 33, 0.3);' title='Deleted Item'";
                }
            ?> onclick="show_location(<?= $coor_origin[0] ?>, <?= $coor_origin[1] ?>, <?= $coor_destination[0] ?>, <?= $coor_destination[1] ?>)">
            <h5 class="mb-4">{{ucfirst($dt->trip_desc)}}</h5>
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="mb-0">Person With</h6>
                    @if($dt->trip_person != null) 
                        <p class="mb-0">{{$dt->trip_person}}</p>
                    @endif
                </div>
                <div>
                    <h6 class="mb-0">Category</h6>
                    <p class="mb-0">{{$dt->trip_category}}</p>
                </div>
            </div>   

            <hr>
            <div class="mt-3 d-flex justify-content-between">
                <div class="text-start">
                    <h6 class="mb-0">From</h6>
                    <p class="mb-0">{{$dt->trip_origin_name}}</p>
                </div>
                <div class="text-center">
                    <a><i class="fa-solid fa-arrow-right"></i></a>
                    <p class="mb-0">{{Converter::calculate_distance($coor_origin[0],$coor_origin[1],$coor_destination[0],$coor_destination[1])}} Km</p>
                </div>
                <div class="text-end">
                    <h6 class="mb-0">Destination</h6>
                    <p class="mb-0">{{$dt->trip_destination_name}}</p>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-start">
                <div>
                    <h6 class="mb-0">Created At</h6>
                    <p class="mb-0">{{date('Y-m-d H:i',strtotime($dt->created_at))}}</p>
                </div>
                @if($dt->updated_at != null) 
                    <div>
                        <h6 class="mb-0">Updated At</h6>
                        <p class="mb-0">{{date('Y-m-d H:i',strtotime($dt->updated_at))}}</p>
                    </div>
                @endif
                @if($dt->deleted_at != null) 
                    <div>
                        <h6 class="mb-0">Deleted At</h6>
                        <p class="mb-0">{{date('Y-m-d H:i',strtotime($dt->deleted_at))}}</p>
                    </div>
                @endif
            </div>
        </button>
    @endforeach
</div>

<script type="text/javascript">
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