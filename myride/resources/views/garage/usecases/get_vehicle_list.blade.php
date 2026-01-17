<style>
    .carousel {
        height: auto !important;
    }
    .carousel-item {
        padding: var(--spaceSM);
        visibility: hidden;
        opacity: 0;
        padding: var(--spaceLG);
        border-radius:var(--roundedLG);
    }
    .carousel-item img {
        object-fit: cover;
    }
    .carousel-indicators {
        gap: var(--spaceLG);
        position: static;
        display: flex;
        margin: var(--spaceMD) 0;
        padding: 0;
    }
    .carousel-indicators button {
        width: 18.5vw !important;
        height: 9.5vw !important;
        margin: 0 !important;
        padding: 0 !important;
        padding: calc(var(--spaceXMD)*0.2) calc(var(--spaceXMD)*0.4);
        justify-content: center;
        align-items: center;
        border-radius: var(--roundedLG);
    }
    .indicator-box {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: white;
        border-radius: var(--roundedLG);
    }
    .indicator-box::after {
        content: "";
        position: absolute;
        border-radius: var(--roundedLG);
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 0;
    }
    .carousel-indicators button::before {
        display: none !important;
    }
    .indicator-top {
        letter-spacing: var(--spaceMini);
        margin: 0;
    }
    .indicator-bottom {
        margin: 0;
    }
    .carousel-indicators button.active .indicator-box {
        transform: scale(1.05);
    }
    .carousel-item.active {
        visibility: visible;
        opacity: 1;
    }
    .indicator-content {
        position: absolute;
        z-index: 2;
        height: 100%;
        left:0;
    }
    .chip {
        margin: 0;
        padding: 0;
    }
    @media (max-width: 767px) {
        .carousel-indicators.mobile-scroll {
            width: 100%;
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto !important;
            overflow-y: hidden;
            white-space: nowrap;
            gap: var(--spaceLG);
            padding-left: var(--spaceSM);
            padding-right: var(--spaceSM);
            margin: 0;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            justify-content: flex-start !important;  
            margin-left: 0 !important;
            left: 0 !important;
            transform: none !important;
        }
        .carousel-indicators.mobile-scroll::-webkit-scrollbar {
            display: none; /* Chrome */
        }
        .carousel-indicators.mobile-scroll button {
            width: 260px !important;
            height: 140px !important;
            flex: 0 0 auto;
        }
        .indicator-box {
            background-size: cover;
            background-position: center;
        }
    }
</style>

<div id="carouselVehicle" class="carousel carousel-dark slide" data-bs-ride="carousel">
    <div class="carousel-indicators" id="vehicle-nav-list"></div>
    <div class="carousel-inner" id="vehicle-content-list"></div>
    <div id="carousel-nav-holder"></div>
    <button id="btnPrev" style="display:none;"></button>    
    <button id="btnNext" style="display:none;"></button>
</div>

<script>
    let page = 1
    const get_all_vehicle = (page) => {
        const holder = 'carouselVehicle'

        $(document).ready(function () {
            // Freeze - Auto Scroll Carousel
            var $carousel = $(`#${holder}`)
            var autoScrollInterval
            var resumeTimeout

            const startAutoScroll = () => {
                stopAutoScroll()
                autoScrollInterval = setInterval(function () {
                    $carousel.carousel('next')
                }, 10000)
            }

            const stopAutoScroll = () => {
                clearInterval(autoScrollInterval)
            }

            const pauseThenResume = () => {
                stopAutoScroll()
                clearTimeout(resumeTimeout)
                resumeTimeout = setTimeout(function () {
                    startAutoScroll()
                }, 600000)
            }

            $.ajax({
                url: `/api/v1/vehicle/header`,
                type: 'GET',
                beforeSend: function (xhr) {
                    Swal.showLoading()
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data.data
                    let ext_class = 'active'
                    $("#vehicle-nav-list").empty()
                    $("#vehicle-content-list").empty()
                    let width = $(window).width()

                    data.forEach((dt,idx) => {
                        const imageUrl = dt.vehicle_img_url ?? `{{ asset('assets/car_default.jpg') }}`
                        
                        $('#vehicle-nav-list').append(`
                            <button data-bs-target="#${holder}" data-bs-slide-to="${idx}" class="${idx === 0 ? 'active' : ''}">
                                <div class="indicator-box" style="background-image:url('${imageUrl}')">
                                    <div class="indicator-content">
                                        <h5 class="text-dark">${dt.vehicle_name}</h5>
                                        <p class="text-dark">${dt.vehicle_type}</p>
                                    </div>
                                </div>
                            </button>
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
                            <div class="carousel-item ${idx === 0 ? "active" : ""} vehicle-carousel" data-bs-interval="10000">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-5 col-md-6">
                                        <img src="${imageUrl}" class="d-block w-100" alt="Vehicle Image">
                                    </div>
                                    <div class="col-xl-8 col-lg-7 col-md-6">
                                        <div class="d-flex justify-content-between position-relative">
                                            <h2>${dt.vehicle_merk} - ${dt.vehicle_name}</h2>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="plate-number m-0">${dt.vehicle_plate_number}</span>
                                            <span class="chip bg-info">${dt.vehicle_type}</span>
                                            <span class="chip bg-info"><i class="fa-solid fa-car"></i> ${dt.vehicle_category}</span>
                                            <span class="chip bg-${markFuel}"><i class="fa-solid fa-gas-pump"></i> ${dt.vehicle_fuel_status}</span>
                                            <span class="chip bg-success"><i class="fa-solid fa-location-arrow"></i> ${dt.vehicle_distance} Km</span>
                                            <span class="chip bg-info"><i class="fa-solid fa-user"></i> ${dt.vehicle_capacity}</span>
                                            <span class="chip bg-info"><i class="fa-solid fa-gears"></i> ${dt.vehicle_transmission}</span>
                                            <span class="chip bg-${markStatus}"><i class="fa-solid fa-wrench"></i> ${dt.vehicle_status}</span>
                                        </div>
                                        <hr>
                                        <p>${dt.vehicle_desc}</p>
                                        ${dt.updated_at ? `<p class="text-secondary text-dark">Last Updated ${dt.updated_at}</p>` : ""}
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-success py-1" href="/garage/detail/${dt.id}"><i class="fa-solid fa-arrow-right"></i> See Detail</a>
                                            <a class="btn btn-warning py-1" href="/garage/edit/${dt.id}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `)
                        ext_class = ''
                    })

                    if(data.length > 3){
                        templateCarouselNavigation("carousel-nav-holder", "carouselVehicle")
                    }
                    
                    // Navigate Carousel Using Keyboard
                    const goPrev = () => {
                        $(`#${holder}`).carousel('prev')
                    }
                    const goNext = () => {
                        $(`#${holder}`).carousel('next')
                    }
                    $(document).on('keydown', function(e) {
                        if (e.key === 'ArrowLeft') goPrev()
                        if (e.key === 'ArrowRight') goNext()
                    });

                    if (data.length > 1 && width < 767) {
                        $("#vehicle-nav-list").addClass("mobile-scroll")
                    }

                    // Init carousel
                    $carousel.carousel({
                        interval: false, 
                        ride: false
                    })

                    $(window).on('resize', function() {
                        if (data.length > 1 && width < 767) {
                            $("#vehicle-nav-list").addClass("mobile-scroll")
                            // Init carousel
                            $carousel.carousel({
                                interval: false, 
                                ride: false
                            })
                        }
                    });
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    if(response.status != 404){
                        generateApiError(response, true)
                    } else {
                        $(`#${holder}`).css('margin-top','var(--spaceMD)')
                        templateAlertContainer(holder, 'no-data', "No vehicle found", 'add a vehicle', '<i class="fa-solid fa-car"></i>','/garage/add')
                    }
                }
            });

            $(document).on('click', `#${holder} .carousel-indicators li`, function () {
                pauseThenResume()
            })
        })
    }
    get_all_vehicle(page)
</script>