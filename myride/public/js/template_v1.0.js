const template_alert_container = (target, type, msg, btn_title, icon, href) => {
    $(`#${target}`).html(`
        <div class="container-fluid alert-container py-2 px-3" style="${type == 'no-data'? 'background-color:rgba(245, 93, 134, 0.2);':''}">
            <div class="d-flex justify-content-start text-start align-items-center flex-wrap gap-4">
                <p class="mb-0" style="font-size: 60px;">${icon}</p>
                <div>
                    <h6>${msg}</h6>
                    ${btn_title != null ? `<a class="btn btn-primary py-1" href=${href}><i class="${type == 'no-data'? 'fa-solid fa-plus':''}"></i> ${ucEachWord(btn_title)}</a>`:''}
                </div>
            </div>
        </div>
    `)
}

const template_carousel_navigation = (holder, carouselId) => {
    $(`#${holder}`).html(`
        <div class="carousel-button-holder">
            <button class="btn btn-primary carousel-control-prev ms-2" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="btn btn-primary carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    `)
}

const template_trip_box = (dt, extra_class = '') => {
    const coorOrigin = dt.trip_origin_coordinate ? dt.trip_origin_coordinate.split(",").map(Number) : null
    const coorDestination = dt.trip_destination_coordinate ? dt.trip_destination_coordinate.split(",").map(Number) : null
    const deletedStyle = dt.deleted_at ? "background-color: rgba(221, 0, 33, 0.3);" : ""
    const deletedTitle = dt.deleted_at ? "title='Deleted Item'" : ""
    const clickFunc = coorOrigin ? `onclick="show_location(${coorOrigin[0]}, ${coorOrigin[1]}, ${coorDestination[0]}, ${coorDestination[1]})"` : ''

    return `
        <button class="container-fluid text-start mb-4 ${extra_class}" style="${deletedStyle}" ${deletedTitle} ${clickFunc}>
            ${dt.vehicle_plate_number ? `<a class="plate-number position-absolute" style="top:calc(-2*var(--spaceSM)); left:calc(-2*var(--spaceSM)); width: fit-content;">${dt.vehicle_plate_number}</a>`:''}
            ${dt.vehicle_name ? `<h6 class="mb-2">${dt.vehicle_name}</h6>`:''}
            <p class="text-secondary">${dt.trip_desc ?? '- No Description Provided -'}</p>
            <hr>
            <div class="mt-3 d-flex justify-content-between flex-wrap align-items-center">
                <div>
                    <h6 class="mb-0">From</h6>
                    <p class="mb-0">${dt.trip_origin_name}</p>
                </div>
                ${ coorOrigin ? `
                    <div class="text-center">
                        <a><i class="fa-solid fa-arrow-right"></i></a>
                        <p class="mb-0" style="font-size:var(--textMD);">${calculate_distance(coorOrigin[0], coorOrigin[1], coorDestination[0], coorDestination[1])} Km</p>
                    </div>`: ''
                }
                <div class="text-end">
                    <h6 class="mb-0">Destination</h6>
                    <p class="mb-0">${dt.trip_destination_name}</p>
                </div>
            </div>
            <div class="collapse" id="collapseDetailTrip${dt.id}">
                <div class="d-flex justify-content-between mt-3">
                    ${dt.trip_person ? `
                        <div>
                            <h6 class="mb-0">Person With</h6>
                            ${dt.trip_person ? `<p class="mb-0">${dt.trip_person}</p>` : "-"}
                        </div>`:''
                    }
                    <div>
                        <h6 class="mb-0">Category</h6>
                        <p class="mb-0">${dt.trip_category}</p>
                    </div>
                </div> 
                ${dt.driver_fullname ? `<div class="mt-3"><h6 class="mb-0">Drive By</h6><p class="mb-0">${dt.driver_fullname}</p></div>` : ''}    
                ${ coorOrigin ? `
                    <a class="btn btn-success py-1 mt-2 btn-set-route" data-trip-origin-coordinate="${dt.trip_origin_coordinate}" data-trip-destination-coordinate="${dt.trip_destination_coordinate}" data-vehicle-type="${dt.vehicle_type}">Set Route on Maps</a>`: ''
                }      
            </div>
            <hr>
            <div class="d-flex justify-content-between flex-wrap align-items-center gap-1">
                <div>
                    <h6 class="mb-0 text-secondary">Created At</h6>
                    <p class="mb-0 text-secondary">${dt.created_at}</p>
                </div>
                ${dt.updated_at ? `
                    <div>
                        <h6 class="mb-0 text-secondary">Updated At</h6>
                        <p class="mb-0 text-secondary">${dt.updated_at}</p>
                    </div>` : ""}
                <div>
                    <a class="btn btn-warning py-2 px-3 btn-update" style="font-size:var(--textMD);" data-vehicle-plate-number="${dt.vehicle_plate_number}" data-id="${dt.id}"
                        data-trip-category="${dt.trip_category}" data-trip-person="${dt.trip_person}" data-trip-destination-name="${dt.trip_destination_name}" data-trip-origin-name="${dt.trip_origin_name}"
                        data-trip-desc="${dt.trip_desc}" data-trip-origin-coordinate="${dt.trip_origin_coordinate}" data-trip-destination-coordinate="${dt.trip_destination_coordinate}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <a class="btn btn-danger py-2 px-3 ms-2 btn-delete" data-url="/api/v1/trip/destroy/${dt.id}" data-context="Trip" style="font-size:var(--textMD);">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <a class="btn btn-primary py-2 px-3 ms-2" data-bs-toggle="collapse" href="#collapseDetailTrip${dt.id}" role="button" aria-expanded="false" style="font-size:var(--textMD);">
                        <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                    </a>
                </div>
            </div>
        </button>
    `;
}

const build_layout_trip = (dt) => {
    if (!dt || !dt.data) return

    const itemsPerSlide = 3
    const carouselInner = $("#carouselTrip .carousel-inner")
    const indicators = $("#carouselTrip .carousel-indicators")

    carouselInner.empty()
    indicators.empty()

    dt.data.forEach((el, i) => {
        const slideIndex = Math.floor(i / itemsPerSlide)

        if ($(`#carouselTrip .carousel-item[data-slide-index="${slideIndex}"]`).length === 0) {
            carouselInner.append(`
                <div class="carousel-item px-2 ${slideIndex === 0 ? "active" : ""}" data-slide-index="${slideIndex}">
                    <div class="holder"></div>
                </div>
            `)

            if(dt.data.length > 3){
                indicators.append(`
                    <button type="button" data-bs-target="#carouselTrip" data-bs-slide-to="${slideIndex}" 
                        class="${slideIndex === 0 ? "active" : ""}" aria-current="${slideIndex === 0 ? "true" : "false"}" aria-label="Slide ${slideIndex + 1}"></button>
                `)
            }
        }

        const targetSlide = $(`#carouselTrip .carousel-item[data-slide-index="${slideIndex}"] .holder`)
        targetSlide.append(template_trip_box(el))
    })
}

const build_delete_modal = (url, context, token, action) => {
    Swal.fire({
        title: "Are you sure?",
        text: `Do you want to delete this "${context}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "No, cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.fire("Deleted!", response.message ?? `Your ${context} has been deleted`, "success").then(() => action())
                },
                error: function() {
                    Swal.fire("Error!", `Failed to delete this ${context}`, "error")
                }
            });
        }
    });
}