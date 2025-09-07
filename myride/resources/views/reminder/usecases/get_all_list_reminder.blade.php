<style>
    .map-board {
        height:300px;
        border-radius: var(--roundedLG);
    }
</style>

<h2>All Reminder</h2>
<table class="table text-center table-bordered">
    <thead>
        <tr>
            <th scope="col" style="width: 140px;">Vehicle</th>
            <th scope="col" style="min-width: 240px;">Title</th>
            <th scope="col">Context & Body</th>
            <th scope="col">Attachment</th>
            <th scope="col" style="width: 160px;">Properties</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody id="reminder-holder"></tbody>
</table>

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
            url: `/api/v1/reminder`,
            type: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Accept", "application/json")
                xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")
                $(`#${holder}`).empty()
            },
            success: function(response) {
                Swal.close()
                const data = response.data.data
                
                data.forEach(dt => {
                    let reminder_attachment_el = ''
                    let class_chip = ''

                    if(dt.reminder_attachment){
                        dt.reminder_attachment.forEach((at,idx) => {
                            reminder_attachment_el += `
                                <a class="chip bg-info fw-normal" data-bs-toggle="modal" data-bs-target="#attachment_${idx}-modal">
                                    <i class="fa-solid ${at.attachment_type == 'location' ? 'fa-location-dot' : at.attachment_type == 'driver' ? 'fa-user' : 'fa-image'}"></i>
                                    ${at.attachment_title}
                                    <div class="modal fade" id="attachment_${idx}-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="text-start">
                                                    ${
                                                        at.attachment_type == 'image' ? `<img src="${at.attachment_value}" class="img-fluid mb-2" alt="attachment">` :
                                                        at.attachment_type == 'location' ? `<div class="map-board" id="map_${idx}-holder"></div>`:''
                                                    }
                                                    <hr>
                                                    <h6 class="mb-0">Attachment</h6>
                                                    <p class="mb-0">${at.attachment_value}</p>
                                                    <h6 class="mb-0">Title</h6>
                                                    <p class="mb-0">${at.attachment_title}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            `

                            if (at.attachment_type === "location") {
                                const coords = at.attachment_value.split(",").map(Number)

                                $(document).on("shown.bs.modal", `#attachment_${idx}-modal`, function () {
                                    initDynamicMap(`map_${idx}-holder`, coords);
                                });
                            }
                        });
                    }

                    if(dt.reminder_context == 'Service'){
                        class_chip = 'bg-danger'
                    } else if(dt.reminder_context == 'Cleaning' || dt.reminder_context == 'Trip'){
                        class_chip = 'bg-success'
                    } 

                    $(`#${holder}`).append(`
                        <tr>
                            <td>${dt.vehicle_plate_number ? `<span class="plate-number">${dt.vehicle_plate_number}</span>`:'-'}</td>
                            <td>${dt.reminder_title}</td>
                            <td>
                                <span class="chip ${class_chip}">${dt.reminder_context}</span>
                                ${dt.reminder_body}
                            </td>
                            <td>${dt.reminder_attachment ? reminder_attachment_el :`-`}</td>
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
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                Swal.close()
                if(response.status != 404){
                    failedMsg('get the reminder')
                } else {
                    $(`#${holder}`).html(`<tr><td colspan="6" id="msg-${holder}"></td></tr>`)
                    template_alert_container(`msg-${holder}`, 'no-data', "No reminder found", 'add a reminder', '<i class="fa-solid fa-clock"></i>','/reminder/add')
                }
                reject(errorThrown)
            }
        });
    };
    get_all_reminder(page)
</script>