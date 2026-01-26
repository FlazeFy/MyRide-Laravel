<div id="sidebar" class="sidebar">
    <h5 class="group-menu">Menu</h5>
    <ul class="nav nav-pills flex-column" id="sidebar_menu-holder">
        <li><a href="/dashboard" class="nav-link <?= $active_menu == "dashboard" ? "active" : "" ?>"><i class="fa-solid fa-table"></i> <span>Dashboard</span></a></li>
        <li><a href="/garage" class="nav-link <?= $active_menu == "garage" ? "active" : "" ?>"><i class="fa-solid fa-warehouse"></i> <span>My Garage</span></a></li>
    </ul>
    <h5 class="group-menu">My Vehicle</h5>
    <ul class="nav nav-pills flex-column" id="vehicle_menu-list"></ul>
    <h5 class="group-menu">Others</h5>
    <ul class="nav nav-pills flex-column">
        <li><a href="/history" class="nav-link <?= $active_menu == "history" ? "active" : "" ?>"><i class="fa-solid fa-clock-rotate-left"></i> <span>History</span></a></li>
        <li><a href="/help" class="nav-link <?= $active_menu == "help" ? "active" : "" ?>"><i class="fa-solid fa-headset"></i> <span>Help Center</span></a></li>
        <li><a href="/about" class="nav-link <?= $active_menu == "about" ? "active" : "" ?>"><i class="fa-solid fa-circle-info"></i> <span>About Us</span></a></li>
    </ul>
</div>

<script>    
    $(document).ready(function() {
        $(".toogle_nav-button").on("click", function() {
            let width = $(window).width()
            if($(this).hasClass("close")){
                $('#sidebar').css({
                    width: "250px",
                    paddingInline: 'var(--spaceMD)',
                    boxShadow: 'rgba(0, 0, 0, 0.5) 0px 7px 17.5px',
                    borderRight: '2px solid var(--secondaryColor)'
                })
                
                if (width < 577) {
                    $('.navbar-collapse-mobile').css({display:'flex',marginTop:'var(--spaceMini)'})   
                    $('.navbar .navbar-brand').css({fontSize:'var(--textMD)',marginBottom:0})   
                }            
            } else {
                $('#sidebar').css({
                    width: "0",
                    paddingInline: '0',
                    boxShadow: 'none',
                    borderRight: '0'
                })
                if (width < 577) {
                    $('.navbar-collapse-mobile').css({display:'none',marginTop:0})   
                    $('.navbar .navbar-brand').css({fontSize:'var(--textXLG)',marginBottom:'var(--spaceXXSM)'})       
                }                              
            }
            
            $(this).find('i').toggleClass('fa-bars fa-circle-xmark')
            $(this).toggleClass('open close') 
            $(this).toggleClass('btn-primary btn-danger')  
        })
    })

    const generateVehicleList = (data,ctx_holder) => {
        $(`#${ctx_holder}`).empty()
        data.forEach(dt => {
            $(`#${ctx_holder}`).append(`
                <li>
                    <span class="plate-number mb-0">${dt.vehicle_plate_number}</span>
                    <a href="/garage/detail/${dt.id}" class="nav-link container p-2 mb-2" style="font-size: var(--textXMD)">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>${dt.vehicle_name}</span> 
                            ${dt.deleted_at ? '<span class="chip-mini bg-danger" title="Deleted" style="padding-inline:var(--spaceMini) !important"><i class="fa-solid fa-triangle-exclamation"></i></span>' : ''}
                        </div>
                    </a>
                </li>
            `)
        })
    }

    const generate_menu = (is_have_vehicle, holder) => {
        if(is_have_vehicle){
            $(`#${holder}`).append(`
                <li><a href="/trip" class="nav-link <?= $active_menu == "trip" ? "active" : "" ?>"><i class="fa-solid fa-suitcase"></i> Trip</a></li>
                <li><a href="/fuel" class="nav-link <?= $active_menu == "fuel" ? "active" : "" ?>"><i class="fa-solid fa-gas-pump"></i> Fuel</a></li>
                <li><a href="/wash" class="nav-link <?= $active_menu == "wash" ? "active" : "" ?>"><i class="fa-solid fa-soap"></i> Wash</a></li>
                <li><a href="/service" class="nav-link <?= $active_menu == "service" ? "active" : "" ?>"><i class="fa-solid fa-screwdriver-wrench"></i> Service</a></li>
                <li><a href="/reminder" class="nav-link <?= $active_menu == "reminder" ? "active" : "" ?>"><i class="fa-solid fa-clock"></i> Reminder</a></li>
                <li><a href="/stats" class="nav-link <?= $active_menu == "stats" ? "active" : "" ?>"><i class="fa-solid fa-chart-simple"></i> Statistic</a></li>
                <li><a href="/driver" class="nav-link <?= $active_menu == "driver" ? "active" : "" ?>"><i class="fa-solid fa-users"></i> Driver</a></li>
                <li><a href="/inventory" class="nav-link <?= $active_menu == "inventory" ? "active" : "" ?>"><i class="fa-solid fa-boxes-stacked"></i> Inventory</a></li>
            `)
        } else {
            $(`#${holder}`).prepend(`<li><a href="/garage/add" class="nav-link"><i class="fa-solid fa-plus"></i> Vehicle</a></li>`)
        }
    }

    const get_vehicle_name = (year) => {
        Swal.showLoading()
        const ctx_holder = "vehicle_menu-list"
        const token = "<?= session()->get('token_key'); ?>"

        const fetchData = () => {
            $.ajax({
                url: `/api/v1/vehicle/name`,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)    
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    localStorage.setItem(ctx_holder,JSON.stringify(data))
                    localStorage.setItem(`last-hit-${ctx_holder}`,Date.now())
                    generate_menu(true, 'sidebar_menu-holder')
                    generateVehicleList(data,ctx_holder)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    Swal.close()
                    if(response.status !== 404 && response.status !== 401){
                        generateApiError(response, true)
                    } else if(response.status === 401){
                        $('#sidebar').css({paddingTop: $(window).width() < 767 ? '120px' : '100px' })
                        templateAlertContainer('sidebar', 'expired_session', "Your session was lost", 'go to login', '<i class="fa-solid fa-arrow-right-to-bracket"></i>','/login')
                    } else {
                        generate_menu(false, ctx_holder)
                    }
                }
            })
        }

        if(ctx_holder in localStorage){
            const lastHit = parseInt(localStorage.getItem(`last-hit-${ctx_holder}`))
            const now = Date.now()

            if(((now - lastHit) / 1000) < listVehicleNameFetchRestTime){
                const data = JSON.parse(localStorage.getItem(ctx_holder))
                if(data){
                    generate_menu(true, 'sidebar_menu-holder')
                    generateVehicleList(data,ctx_holder)
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