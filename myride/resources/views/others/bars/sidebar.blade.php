<div class="sidebar p-3">
    <h5 class="group-menu">Menu</h5>
    <ul class="nav nav-pills flex-column">
        <li><a href="/dashboard" class="nav-link active">Dashboard</a></li>
        <li><a href="/garage" class="nav-link">My Garage</a></li>
        <li><a href="/trip" class="nav-link">Trip</a></li>
        <li><a href="/fuel" class="nav-link">Fuel</a></li>
        <li><a href="/clean" class="nav-link">Cleanliness</a></li>
        <li><a href="/service" class="nav-link">Service</a></li>
        <li><a href="/reminder" class="nav-link">Reminder</a></li>
        <li><a href="/stats" class="nav-link">Statistic</a></li>
        <li><a href="/driver" class="nav-link">Driver</a></li>
    </ul>
    <h5 class="group-menu">Vehicle</h5>
    <ul class="nav nav-pills flex-column" id="vehicle_menu-list"></ul>
    <h5 class="group-menu">Others</h5>
    <ul class="nav nav-pills flex-column">
        <li><a href="/setting" class="nav-link">Setting</a></li>
        <li><a href="/history" class="nav-link">History</a></li>
        <li><a href="/help" class="nav-link">Help Center</a></li>
        <li><a href="/about" class="nav-link">About Us</a></li>
    </ul>
</div>

<script>
    const generate_vehicle_list = (data,ctx_holder) => {
        $(`#${ctx_holder}`).empty()
        data.forEach(dt => {
            $(`#${ctx_holder}`).append(`
                <li>
                    <span class="container py-1 px-2 mx-2 fw-bold mb-0" style="font-size:var(--textMD); background:var(--warningColor);">${dt.vehicle_plate_number}</span>
                    <a href="/garage/detail/${dt.id}" class="nav-link container p-2 mb-2" style="font-size:var(--textXMD);">${dt.vehicle_name}</a>
                </li>
            `)
        });
    }

    const get_vehicle_name = (year) => {
        Swal.showLoading()
        const ctx_holder = "vehicle_menu-list"

        const failedMsg = () => {
            Swal.fire({
                title: "Oops!",
                text: `Failed to get the vehicle list`,
                icon: "error"
            });
        }
        const fetchData = () => {
            $.ajax({
                url: `/api/v1/vehicle/name`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", "Bearer <?= session()->get("token_key"); ?>")    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx_holder,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx_holder}`,Date.now())
                    generate_vehicle_list(data,ctx_holder)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg()
                    } else {
                        $(`#${ctx_holder}`).prepend(`<li><a href="/garage/add" class="nav-link">Add Vehicle</a></li>`)
                    }
                }
            });
        }

        if(ctx_holder in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx_holder}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < listVehicleNameFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx_holder))
                if(data){
                    generate_vehicle_list(data,ctx_holder)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg()
                }
            } else {
                fetchData()
            }
        } else {
            fetchData()
        }
    }
    get_vehicle_name()
</script>