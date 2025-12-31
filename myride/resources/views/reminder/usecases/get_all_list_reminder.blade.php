<style>
    .map-board {
        height:300px;
        border-radius: var(--roundedLG);
    }
</style>

<h2>All Reminder</h2><hr>
<div class="table-responsive">
    <table class="table text-center table-bordered">
        <thead>
            <tr>
                <th scope="col" style="min-width: 160px;">Vehicle</th>
                <th scope="col" style="min-width: 240px;">Title</th>
                <th scope="col" style="min-width: 240px;">Context & Body</th>
                <th scope="col">Attachment</th>
                <th scope="col" style="min-width: 160px;">Properties</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="reminder-holder"></tbody>
    </table>
</div>

<script>
    let page = 1

    const initDynamicMap = (elementId, coords) => {
        const map = new google.maps.Map(document.getElementById(elementId), {
            center: { lat: coords[0], lng: coords[1] },
            zoom: 14,
        });

        new google.maps.Marker({
            position: { lat: coords[0], lng: coords[1] },
            map: map,
        });
    }

    const get_all_reminder = (page) => {
        const holder = 'reminder-holder'

        Swal.showLoading();
        $.ajax({
            url: `/api/v1/reminder?page=${page}`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                const current_page = response.data.current_page
                const total_page = response.data.last_page
                
                data.forEach(dt => {
                    let reminder_attachment_el = ''
                    let class_chip = ''

                    if(dt.reminder_attachment){
                        dt.reminder_attachment.forEach((at,idx) => {
                            reminder_attachment_el += `
                                <a class="chip bg-info fw-normal m-0" data-bs-toggle="modal" data-bs-target="#attachment_${dt.id}_${idx}-modal">
                                    <i class="fa-solid ${at.attachment_type == 'location' ? 'fa-location-dot' : at.attachment_type == 'driver' ? 'fa-user' : 'fa-image'}"></i> ${ucFirst(at.attachment_type)}
                                </a>
                                <div class="modal fade" id="attachment_${dt.id}_${idx}-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title fw-bold" id="exampleModalLabel">Attachment ${ucFirst(at.attachment_type)}</h4>
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                ${
                                                    at.attachment_type == 'image' ? `<img src="${at.attachment_value}" class="img-fluid mb-2" alt="attachment">` :
                                                    at.attachment_type == 'location' ? `
                                                        <div class="map-board" id="map_${dt.id}_${idx}-holder"></div>
                                                        <a class="btn btn-success w-100 mt-3 btn-set-route" data-trip-origin-coordinate="now" data-trip-destination-coordinate="${at.attachment_value.split(",").map(Number)}" data-vehicle-type="undefined"><i class="fa-solid fa-map-pin" aria-hidden="true"></i> Set Route</a>
                                                    `:''
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `

                            if (at.attachment_type === "location") {
                                const coords = at.attachment_value.split(",").map(Number)

                                $(document).on("shown.bs.modal", `#attachment_${dt.id}_${idx}-modal`, function () {
                                    initDynamicMap(`map_${dt.id}_${idx}-holder`, coords);
                                });
                            }
                        });
                    }

                    if(dt.reminder_context.includes(['Service','Pick Up','Drop Off','Trip'])){
                        class_chip = 'bg-danger'
                    } else {
                        class_chip = 'bg-success'
                    }

                    $(`#${holder}`).append(`
                        <tr>
                            <td>${dt.vehicle_plate_number ? `<span class="plate-number">${dt.vehicle_plate_number}</span>`:'-'}</td>
                            <td>${dt.reminder_title}</td>
                            <td>
                                <div class="d-flex flex-wrap text-start align-items-center">
                                    <span class="chip mb-0 ms-0 ${class_chip}">${dt.reminder_context}</span>
                                    ${dt.reminder_body}
                                </div>
                            </td>
                            <td>${dt.reminder_attachment ? `<div class="d-flex flex-wrap gap-2 justify-content-center">${reminder_attachment_el}</div>` :`-`}</td>
                            <td class="text-start">
                                <h6 class="mb-0">Remind At</h6>
                                <p class="mb-0">${dt.remind_at}</p>
                                <h6 class="mb-0">Created At</h6>
                                <p class="mb-0 text-secondary">${getDateToContext(dt.created_at,'calendar')}</p>
                            </td>
                            <td>
                                <a class="btn btn-danger btn-delete" style="width:50px;" data-url="/api/v1/reminder/destroy/${dt.id}" data-context="Reminder"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    `)
                });

                generatePagination(holder, get_all_reminder, total_page, current_page)
                initStaticModal()
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    generateApiError(response, true)
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    templateAlertContainer(`msg-${holder}`, 'no-data', "No reminder found", 'add a reminder', '<i class="fa-solid fa-clock"></i>','/reminder/add')
                }
                reject(errorThrown)
            }
        });
    }
    get_all_reminder(page)
</script>