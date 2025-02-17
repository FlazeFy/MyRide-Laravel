const template_alert_container = (target, type, msg, btn_title, icon, href) => {
    $(`#${target}`).html(`
        <div class="container p-3" style="${type == 'no-data'? 'background-color:rgba(59, 131, 246, 0.2);':''}">
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

const template_trip_box = (dt,target_holder) => {
    const coorOrigin = dt.trip_origin_coordinate.split(", ").map(Number)
    const coorDestination = dt.trip_destination_coordinate.split(", ").map(Number)
    const deletedStyle = dt.deleted_at ? "background-color: rgba(221, 0, 33, 0.3);" : ""
    const deletedTitle = dt.deleted_at ? "title='Deleted Item'" : ""
    
    $(target_holder).append(`
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