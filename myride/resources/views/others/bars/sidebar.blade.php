<div class="sidebar p-3">
    <h5 class="group-menu">Menu</h5>
    <ul class="nav nav-pills flex-column" id="sidebar_menu-holder">
        <li><a href="/dashboard" class="nav-link <?= $active_menu == "dashboard" ? "active" : "" ?>"><i class="fa-solid fa-table"></i> Dashboard</a></li>
        <li><a href="/garage" class="nav-link <?= $active_menu == "garage" ? "active" : "" ?>"><i class="fa-solid fa-warehouse"></i> My Garage</a></li>
    </ul>
    <h5 class="group-menu">Vehicle</h5>
    <ul class="nav nav-pills flex-column" id="vehicle_menu-list"></ul>
    <h5 class="group-menu">Others</h5>
    <ul class="nav nav-pills flex-column">
        <li><a href="/setting" class="nav-link <?= $active_menu == "setting" ? "active" : "" ?>"><i class="fa-solid fa-gear"></i> Setting</a></li>
        <li><a href="/history" class="nav-link <?= $active_menu == "history" ? "active" : "" ?>"><i class="fa-solid fa-clock-rotate-left"></i> History</a></li>
        <li><a href="/help" class="nav-link <?= $active_menu == "help" ? "active" : "" ?>"><i class="fa-solid fa-headset"></i> Help Center</a></li>
        <li><a href="/about" class="nav-link <?= $active_menu == "about" ? "active" : "" ?>"><i class="fa-solid fa-circle-info"></i> About Us</a></li>
    </ul>
</div>

<script>
    const generate_vehicle_list = (data,ctx_holder) => {
        $(`#${ctx_holder}`).empty()
        data.forEach(dt => {
            $(`#${ctx_holder}`).append(`
                <li>
                    <span class="plate-number mb-0">${dt.vehicle_plate_number}</span>
                    <a href="/garage/detail/${dt.id}" class="nav-link container p-2 mb-2" style="font-size:var(--textXMD);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>${dt.vehicle_name}</span> 
                            ${dt.deleted_at ? '<span class="chip-mini bg-danger">Deleted</span>' : ''}
                        </div>
                    </a>
                </li>
            `)
        });
    }

    const generate_menu = (is_have_vehicle, holder) => {
        if(is_have_vehicle){
            $(`#${holder}`).append(`
                <li><a href="/trip" class="nav-link <?= $active_menu == "trip" ? "active" : "" ?>"><i class="fa-solid fa-suitcase"></i> Trip</a></li>
                <li><a href="/fuel" class="nav-link <?= $active_menu == "fuel" ? "active" : "" ?>"><i class="fa-solid fa-gas-pump"></i> Fuel</a></li>
                <li><a href="/clean" class="nav-link <?= $active_menu == "clean" ? "active" : "" ?>"><i class="fa-solid fa-soap"></i> Cleanliness</a></li>
                <li><a href="/service" class="nav-link <?= $active_menu == "service" ? "active" : "" ?>"><i class="fa-solid fa-screwdriver-wrench"></i> Service</a></li>
                <li><a href="/reminder" class="nav-link <?= $active_menu == "reminder" ? "active" : "" ?>"><i class="fa-solid fa-clock"></i> Reminder</a></li>
                <li><a href="/stats" class="nav-link <?= $active_menu == "stats" ? "active" : "" ?>"><i class="fa-solid fa-chart-simple"></i> Statistic</a></li>
                <li><a href="/driver" class="nav-link <?= $active_menu == "driver" ? "active" : "" ?>"><i class="fa-solid fa-users"></i> Driver</a></li>
                <li><a href="/inventory" class="nav-link <?= $active_menu == "inventory" ? "active" : "" ?>"><i class="fa-solid fa-boxes-stacked"></i> Inventory</a></li>
            `)
        } else {
            $(`#${holder}`).prepend(`<li><a href="/garage/add" class="nav-link"><i class="fa-solid fa-plus"></i> Add Vehicle</a></li>`)
        }
    }

    const get_vehicle_name = (year) => {
        Swal.showLoading()
        const ctx_holder = "vehicle_menu-list"

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
                    generate_menu(true, 'sidebar_menu-holder')
                    generate_vehicle_list(data,ctx_holder)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status != 404){
                        failedMsg(`get the vehicle`)
                    } else {
                        generate_menu(false, ctx_holder)
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
                    generate_menu(true, 'sidebar_menu-holder')
                    generate_vehicle_list(data,ctx_holder)
                    Swal.close()
                } else {
                    Swal.close()
                    failedMsg(`get the vehicle`)
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