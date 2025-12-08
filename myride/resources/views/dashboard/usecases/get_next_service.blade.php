<h2>Next Service</h2><hr>
<div id="next_service-holder"></div>

<script>
    const get_service = () => {
        Swal.showLoading()
        const ctx = 'next_service'

        const generate_summary = (remind_at,service_note,service_price_total,service_location,vehicle_plate_number,service_category) => {
            const dateObj = new Date(remind_at.replace(" ", "T"))
            const date = dateObj.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })
            const time = dateObj.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit", hour12: true })
            let displayDate = date
            let chipClass = "bg-success"
            const now = new Date()

            const tomorrow = new Date(now)
            tomorrow.setDate(now.getDate() + 1)

            const isTomorrow = dateObj.getDate() === tomorrow.getDate() && dateObj.getMonth() === tomorrow.getMonth() && dateObj.getFullYear() === tomorrow.getFullYear()

            if (isTomorrow) {
                displayDate = "Tomorrow"
                chipClass = "bg-warning"
            }

            const diffHours = (dateObj - now) / (1000 * 60 * 60)
            if (diffHours > 0 && diffHours < 12) {
                chipClass = "bg-danger"
            }

            $(`#${ctx}-holder`).html(`
                <h4 class="fw-bold">${displayDate}</h4>
                <span class="d-flex justify-align-center align-items-center justify-content-center">
                    <h4 class="fw-bold chip ${chipClass} d-inline-block me-0">${time}</h4>
                    ${vehicle_plate_number ? `<div class="plate-number">${vehicle_plate_number}</div>` :''}
                    <h4 class="fw-bold chip ${chipClass} d-inline-block me-0">${service_category}</h4>
                </span>
                <p class="text-secondary mb-0"><b>Note:</b> ${service_note}</p>
                <p class="text-secondary mb-0"><b>Location:</b> ${service_location}</p>
            `)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/service/next`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx}`,Date.now())
                    generate_summary(data.remind_at,data.service_note,data.service_price_total,data.service_location,data.vehicle_plate_number,data.service_category)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()

                    if(response.status != 404){
                        generate_api_error(response, true)
                    } else {
                        message_short_image(`${ctx}-holder`,`{{asset('assets/empty.png')}}`,`there's no active service`)
                    }
                }
            });
        }

        if(ctx in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < summaryFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx))
                if(data){
                    generate_summary(data.remind_at,data.service_note,data.service_price_total,data.service_location,data.vehicle_plate_number,data.service_category)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg('get the service')
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_service()
</script>