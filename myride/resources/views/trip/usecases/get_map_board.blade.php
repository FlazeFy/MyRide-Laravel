<style>
    #map-board {
        height:70vh;
        border-radius: 0 0 15px 15px;
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
        position: absolute;
        top: -30px;
        right: -15px;
    }
    .gm-control-active span {
        background: white !important;
    }

    .maps-toolbar {
        border-radius: 20px;
        border: 5px solid black;
        padding: 0 !important;
        background: black;
    }
    .maps-toolbar button {
        margin: 10px !important;
        border-radius: 10px !important;
    }
</style>

<div class="maps-toolbar">
    <div class="d-flex justify-content-end">
        
    </div>
    <div class="position-relative">
        <div id="map-board"></div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXu2ivsJ8Hj6Qg1punir1LR2kY9Q_MSq8&callback=initMap&v=weekly" defer></script>

<script type="text/javascript">
    let map;

    function initMap() {
        //Map starter
        var markers = [
            <?php 
                foreach($dt_all_trip_location as $dt){
                    $coor_destination = explode(", ", $dt->trip_destination_coordinate);
                    $coor_origin = explode(", ", $dt->trip_origin_coordinate);

                    echo "{
                        coords: {lat: "; echo $coor_origin[0]; echo", lng: "; echo $coor_origin[1]; echo"},
                        content: 
                        `<div>
                            <h6>$dt->trip_desc</h6>
                            <span class='bg-dark rounded-pill px-2 py-1 text-white'>$dt->trip_category</span>
                            ";
                            if($dt->trip_origin_name){
                                echo "<p class='mt-2 mb-0 fw-bold'>Origin</p>
                                <p>$dt->trip_origin_name</p>";
                            }
                            echo"
                            <p class='mt-2 mb-0 fw-bold'>Created At</p>
                            <p>"; echo date("Y-m-d H:i",strtotime($dt->created_at)); echo"</p>
                            <a class='btn btn-dark rounded-pill px-2 py-1' style='font-size:12px;'><i class='fa-solid fa-location-arrow'></i> Set Direction</a>
                        </div>`
                    },{
                        coords: {lat: "; echo $coor_destination[0]; echo", lng: "; echo $coor_destination[1]; echo"},
                        content: 
                        `<div>
                            <h6>$dt->trip_desc</h6>
                            <span class='bg-dark rounded-pill px-2 py-1 text-white'>$dt->trip_category</span>
                            ";
                            if($dt->trip_destination_name){
                                echo "<p class='mt-2 mb-0 fw-bold'>Destination</p>
                                <p>$dt->trip_destination_name</p>";
                            }
                            echo"
                            <p class='mt-2 mb-0 fw-bold'>Created At</p>
                            <p>"; echo date("Y-m-d H:i",strtotime($dt->created_at)); echo"</p>
                            <a class='btn btn-dark rounded-pill px-2 py-1' style='font-size:12px;'><i class='fa-solid fa-location-arrow'></i> Set Direction</a>
                        </div>`
                    },";
                }
            ?>
        ];

        map = new google.maps.Map(document.getElementById("map-board"), {
            center: { lat: -6.226838579766097, lng: 106.82157923228753},
            zoom: 12,
        });

        <?php 
            if($dt_all_trip_location){
                $total = count($dt_all_trip_location);

                for($i = 0; $i < $total; $i++){
                    echo "addMarker(markers[".$i."]);";
                }
            }
        ?>

        function addMarker(props){
            var marker = new google.maps.Marker({
                position: props.coords,
                map: map,
                icon: props.icon
            });

            if(props.iconImage){
                marker.setIcon(props.iconImage);
            }
            if(props.content){
                var infoWindow = new google.maps.InfoWindow({
                content:props.content
            });
            marker.addListener('click', function(){
                infoWindow.open(map, marker);
            });
            }
        }
    }

    window.initMap = initMap;
</script>