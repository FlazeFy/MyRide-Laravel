const template_alert_container = (target, type, msg, btn_title, icon, href) => {
    $(`#${target}`).html(`
        <div class="container p-3" style="${type == 'no-data'? 'background-color:rgba(245, 93, 134, 0.2);':''}">
            <div class="d-flex justify-content-start">
                <div class="me-3">
                    <h1 style="font-size: 70px;">${icon}</h1>
                </div>
                <div>
                    <h4>${msg}</h4>
                    ${btn_title != null ? `<a class="btn btn-primary mt-3" href=${href}><i class="${type == 'no-data'? 'fa-solid fa-plus':''}"></i> ${ucEachWord(btn_title)}</a>`:''}
                </div>
            </div>
        </div>
    `)
}

const template_trip_box = (dt, extra_class = '') => {
    const coorOrigin = dt.trip_origin_coordinate.split(",").map(Number)
    const coorDestination = dt.trip_destination_coordinate.split(",").map(Number)
    const deletedStyle = dt.deleted_at ? "background-color: rgba(221, 0, 33, 0.3);" : ""
    const deletedTitle = dt.deleted_at ? "title='Deleted Item'" : ""

    return `
        <button class="container text-start mb-4 ${extra_class}" style="${deletedStyle}" ${deletedTitle} 
            onclick="show_location(${coorOrigin[0]}, ${coorOrigin[1]}, ${coorDestination[0]}, ${coorDestination[1]})">
            ${dt.vehicle_plate_number ? `<a class="plate-number position-absolute" style="top:calc(-2*var(--spaceSM)); left:calc(-2*var(--spaceSM)); width: fit-content;">${dt.vehicle_plate_number}</a>`:''}
            ${dt.vehicle_name ? `<h6 class="mb-2">${dt.vehicle_name}</h6>`:''}
            <p class="text-secondary">${dt.trip_desc}</p>
            <hr>
            <div class="mt-3 d-flex justify-content-between">
                <div>
                    <h6 class="mb-0">From</h6>
                    <p class="mb-0">${dt.trip_origin_name}</p>
                </div>
                <div class="text-center">
                    <a><i class="fa-solid fa-arrow-right fa-xs"></i></a>
                    <p class="mb-0" style="font-size:var(--textMD);">${calculate_distance(coorOrigin[0], coorOrigin[1], coorDestination[0], coorDestination[1])} Km</p>
                </div>
                <div class="text-end">
                    <h6 class="mb-0">Destination</h6>
                    <p class="mb-0">${dt.trip_destination_name}</p>
                </div>
            </div>
            <div class="collapse" id="collapseDetailTrip${dt.id}">
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <h6 class="mb-0">Person With</h6>
                        ${dt.trip_person ? `<p class="mb-0">${dt.trip_person}</p>` : ""}
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0">Category</h6>
                        <p class="mb-0">${dt.trip_category}</p>
                    </div>
                </div> 
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="mb-0 text-secondary">Created At</h6>
                    <p class="mb-0 text-secondary">${dt.created_at}</p>
                </div>
                ${dt.updated_at ? `
                    <div>
                        <h6 class="mb-0 text-secondary">Updated At</h6>
                        <p class="mb-0 text-secondary">${dt.updated_at}</p>
                    </div>` : ""}
                <a class="btn btn-primary pt-2 pb-1 px-3 ms-2" data-bs-toggle="collapse" href="#collapseDetailTrip${dt.id}" role="button" aria-expanded="false" style="font-size:var(--textMD);">See Detail</a>
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

            indicators.append(`
                <button type="button" data-bs-target="#carouselTrip" data-bs-slide-to="${slideIndex}" 
                    class="${slideIndex === 0 ? "active" : ""}" aria-current="${slideIndex === 0 ? "true" : "false"}" aria-label="Slide ${slideIndex + 1}"></button>
            `)
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