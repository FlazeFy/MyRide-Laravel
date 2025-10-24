<h4>Last Trip</h4>
<div id="last_trip-holder"></div>

<script>
    const get_last_trip = () => {
        Swal.showLoading()
        const ctx = 'last_trip'

        const generate_last_trip = (created_at,trip_destination_name,trip_destination_coordinate,vehicle_plate_number,driver_username) => {
            const dateObj = new Date(created_at.replace(" ", "T"))
            const date = dateObj.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" })

            $(`#${ctx}-holder`).html(`
                <h4 class="fw-bold">${date}</h4>
                <span class="d-flex justify-align-center justify-content-center">
                    <a class="plate-number">${vehicle_plate_number}</a>
                    ${driver_username ? `<a class="plate-number"><i class="fa-solid fa-user-tie"></i> ${driver_username}</a>` :''}
                </span>
                <p class="text-secondary mb-0"><b>Locate on:</b> ${trip_destination_coordinate} | ${trip_destination_name}</p>
            `)
        }

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/trip/last`,
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
                    generate_last_trip(data.created_at,data.trip_destination_name,data.trip_destination_coordinate,data.vehicle_plate_number,data.driver_username)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()

                    if(response.status != 404){
                        failedMsg('get the last trip')
                    } else {
                        message_short_image(`${ctx}-holder`,`{{asset('assets/empty.png')}}`,`there's no last the trip history`)
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
                    generate_last_trip(data.created_at,data.trip_destination_name,data.trip_destination_coordinate,data.vehicle_plate_number,data.driver_username)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg('get the last trip history')
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_last_trip()
</script>