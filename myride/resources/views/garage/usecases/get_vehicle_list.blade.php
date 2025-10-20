<style>
    .carousel {
        position: relative;
        margin-top: 200px;
    }
    .carousel-item {
        min-height: 360px;
    }
    .carousel-item img {
        border-radius: var(--roundedLG);
        border: 2px solid var(--darkColor);
    }
    .carousel-caption {
        right: 0 !important;
        left: 0 !important;
        background: linear-gradient(to bottom, var(--warningColor), var(--secondaryWarningColor)) !important;
        bottom: 0 !important;
        margin: 0 var(--spaceLG);
        text-align: left !important;
        border-radius: var(--roundedLG);
        height: 100%;
        padding: var(--spaceJumbo) var(--spaceJumbo) 16vh var(--spaceJumbo) !important;
    }
    .carousel-indicators {
        position: absolute;
        top: -200px;
        left: 0;
        right: 0;
        margin: var(--spaceXLG) !important;
        display: block;         
        white-space: nowrap;    
        overflow-x: auto;        
        overflow-y: hidden;
        -ms-overflow-style: none; 
        scrollbar-width: none;
    }
    .carousel-indicators::-webkit-scrollbar { 
        display: none; 
    }
    .carousel-indicators button {
        display: inline-block;    
        vertical-align: top;
        height: 10vh !important;
        width: 18vh !important;
        margin: 0 var(--spaceMD) !important;
        border-width: var(--spaceMini);
        border-radius: var(--roundedXLG);
    }
    .carousel-indicators button:hover {
        border: var(--spaceMini) solid var(--primaryColor);
    }
    .carousel-button-holder {
        right: var(--spaceLG);
    }
</style>

<div id="carouselVehicle" class="carousel carousel-dark slide" data-bs-ride="carousel">
    <div class="carousel-indicators" id="vehicle-nav-list"></div>
    <div class="carousel-inner" id="vehicle-content-list"></div>
    <div id="carousel-nav-holder"></div>
</div>

<script>
    let page = 1
    const get_all_vehicle = (page) => {
        Swal.showLoading()
        $.ajax({
            url: `/api/v1/vehicle/header`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                let ext_class = 'active'
                $("#vehicle-nav-list").empty()
                $("#vehicle-content-list").empty()

                data.forEach((dt,idx) => {
                    $('#vehicle-nav-list').append(`
                        <button type="button" data-bs-target="#carouselExampleDark" style="background-image: linear-gradient(rgba(0, 0, 0, 0.2),rgba(0, 0, 0, 0.35)), url('http://127.0.0.1:8000/assets/car_default.jpg');" 
                        data-bs-slide-to="${idx}" class="active" aria-current="true" aria-label="Slide ${idx + 1}"></button>
                    `)

                    let markFuel = "success"
                    if (dt.vehicle_fuel_status === "Empty"){
                        markFuel = "danger"
                    } else if (dt.vehicle_fuel_status === "Low"){
                        markFuel = "warning"
                    }
                    let markStatus = "success"
                    if (dt.vehicle_status === "Broken"){ 
                        markStatus = "warning"
                    } else if (dt.vehicle_status === "Fatal Broken"){
                        markStatus = "danger"
                    }

                    $("#vehicle-content-list").append(`
                        <div class="carousel-item ${idx === 0 ? "active" : ""}" data-bs-interval="10000">
                            <div class="carousel-caption">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-5 col-md-12">
                                        <img src="${dt.vehicle_image || "/assets/car_default.jpg"}" class="d-block w-100" alt="Vehicle Image">
                                    </div>
                                    <div class="col-xl-8 col-lg-7 col-md-12">
                                        <div class="d-flex justify-content-between position-relative">
                                            <div class="bg-warning py-2 px-4 position-absolute rounded text-dark fw-bold" style="top: -110px;">
                                                <div class="w-100 rounded-pill py-2 mb-1" style="background:${dt.vehicle_color}; height: var(--spaceMD);"></div>Color
                                            </div>
                                            <h2>${dt.vehicle_merk} - ${dt.vehicle_name}</h2>
                                        </div>
                                        <div>
                                            <span class="plate-number mb-0">${dt.vehicle_plate_number}</span>
                                            <span class="chip bg-info">${dt.vehicle_type}</span>
                                            <span class="chip bg-info"><i class="fa-solid fa-car"></i> ${dt.vehicle_category}</span>
                                            <span class="chip bg-${markFuel}"><i class="fa-solid fa-gas-pump"></i> ${dt.vehicle_fuel_status}</span>
                                            <span class="chip bg-success"><i class="fa-solid fa-location-arrow"></i> ${dt.vehicle_distance} Km</span>
                                            <span class="chip bg-info"><i class="fa-solid fa-user"></i> ${dt.vehicle_capacity}</span>
                                            <span class="chip bg-${markStatus}"><i class="fa-solid fa-wrench"></i> ${dt.vehicle_status}</span>
                                        </div>
                                        <hr>
                                        <p>${dt.vehicle_desc}</p>
                                        ${dt.updated_at ? `<p class="text-secondary text-dark">Last Updated ${dt.updated_at}</p>` : ""}
                                        <a class="btn btn-success me-2" href="/garage/detail/${dt.id}"><i class="fa-solid fa-arrow-right"></i> See Detail</a>
                                        <a class="btn btn-warning" href="/garage/edit/${dt.id}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `)
                    ext_class = ''
                })

                if(data.length > 3){
                    template_carousel_navigation("carousel-nav-holder", "<?= $carouselId ?>")
                }
                    
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                Swal.fire({
                    title: "Oops!",
                    text: "Something went wrong",
                    icon: "error"
                });
            }
        });
    }
    get_all_vehicle(page)
</script>