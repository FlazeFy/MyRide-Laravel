<style>
    .carousel-inner, .carousel-item {
        height: 100% !important;
        max-height: 100vh !important;
    }
    .carousel-caption {
        color: var(--whiteColor) !important;
        right: 0 !important;
        left: 0 !important;
        background: var(--darkColor) !important;
        bottom: 0 !important;
        margin: 0;
        width: 100%;
        text-align: left !important;
        padding: var(--spaceJumbo) var(--spaceJumbo) 16vh var(--spaceJumbo) !important;
    }
    .carousel-indicators {
        display: block !important;
        margin: var(--spaceXLG) !important;
    }
    .carousel-indicators button {
        height: 10vh !important;
        width: 18vh !important;
        margin: 0 var(--spaceMD) !important;
        border-width: var(--spaceMini);
    }
    .carousel-indicators button:hover {
        border: var(--spaceMini) solid var(--primaryColor);
    }
    .carousel-control-prev, .carousel-control-next {
        margin-top: 25vh !important;
    }
    .btn-danger {
        position:absolute !important;
    }
</style>

<div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
    <div class="carousel-indicators" id="vehicle-nav-list"></div>
    <div class="carousel-inner" id="vehicle-content-list"></div>
    <button class="carousel-control-prev vehicle-other ms-4" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next vehicle-other me-4" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
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
                            <img src="${dt.vehicle_image || "/assets/car_default.jpg"}" class="d-block w-100" alt="Vehicle Image">
                            <div class="carousel-caption">
                                <div class="d-flex justify-content-between position-relative">
                                    <div class="bg-dark py-2 px-4 position-absolute rounded-pill" style="top: -110px;">
                                        <div class="w-100 rounded-pill py-2 mb-1" style="background:${dt.vehicle_color}; height: var(--spaceMD);"></div>Color
                                    </div>
                                    <div><h1>${dt.vehicle_merk} - ${dt.vehicle_name}</h1></div>
                                    <div>
                                        <span class="bg-success text-white px-4 py-3 rounded-pill me-2">${dt.vehicle_type}</span>
                                        <span class="bg-primary text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-car"></i> ${dt.vehicle_category}</span>
                                        <span class="bg-${markFuel} text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-gas-pump"></i> ${dt.vehicle_fuel_status}</span>
                                        <span class="bg-success text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-location-arrow"></i> ${dt.vehicle_distance} Km</span>
                                        <span class="bg-primary text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-user"></i> ${dt.vehicle_capacity}</span>
                                        <span class="bg-${markStatus} text-white px-4 py-3 rounded-pill me-2"><i class="fa-solid fa-wrench"></i> ${dt.vehicle_status}</span>
                                    </div>
                                </div>
                                <h3>${dt.vehicle_plate_number}</h3>
                                <hr>
                                <h5>${dt.vehicle_desc}</h5>
                                <a class="btn btn-success rounded-pill py-2 px-5 my-2 me-2" href="/garage/detail/${dt.id}"><i class="fa-solid fa-arrow-right"></i> See Detail</a>
                                <a class="btn btn-warning rounded-pill py-2 px-5 my-2 text-white" href="/garage/edit/${dt.id}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                ${dt.updated_at ? `<h6 class="fst-italic" style="font-size: var(--textXMD); text-align: end;">Last Updated ${dt.updated_at}</h6>` : ""}
                            </div>
                        </div>
                    `)
                    ext_class = ''
                })
                    
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