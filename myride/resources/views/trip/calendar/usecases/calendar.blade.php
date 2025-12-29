<style>
    #calendar {
        width: 100%;
    }
    /* Main */
    #calendar a {
        text-decoration: none !important;
        color:var(--darkColor);
    }

    .fc-h-event{
        border: none;
        border-radius: var(--roundedMini);
    }

    /* Toolbar */
    .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr{
        position: relative;
    }
    .fc-toolbar-chunk .fc-button-group button, .fc-today-button.fc-button.fc-button-primary {
        border-left: none !important;
        border-right: none !important;
        border-top: none !important;
    }
    .fc-header-toolbar.fc-toolbar.fc-toolbar-ltr .fc-toolbar-chunk:nth-child(2n)  {
        position: absolute;
        margin-left: 18vh;
    }
    .calendar-tag-holder {
        position: absolute; 
        right: 20vh; 
        z-index: 99;
    }
    .calendar-tag-holder .btn-primary{
        color: var(--secondaryColor) !important;
        z-index: 99 !important;
    }
    .fc-button-group {
        display: inline-flex !important;
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
    }

    /* Day Grid */
    .fc-daygrid-event-dot{
        border:none !important;
    }
    .fc .fc-daygrid-day.fc-day-today{
        background: transparent;
    }
    .fc .fc-daygrid-day.fc-day-today a.fc-daygrid-day-number{
        background: var(--warningColor);
        border-radius: var(--roundedCircle);
        width: 25px;
        height: 25px;
        margin: var(--spaceMini);
        color: var(--darkColor) !important;
        padding-top: calc(var(--spaceMini) - 1px)!important; 
        padding-inline: var(--spaceMini)!important; 
    }
    a.fc-daygrid-day-number{
        font-size: var(--textXMD); 
    }
    .fc-event-time{
        display:none;
    }

    /* Day Event */
    .fc-event-title{
        color: var(--darkColor)!important;
        white-space: normal !important;
        font-weight: 500;
        text-align: start;
    }
    .fc-daygrid-event, .fc-timegrid-event{
        -webkit-transition: all 0.4s;
        -o-transition: all 0.4s;
        transition: all 0.4s;
        z-index: 999 !important;
    }
    th.fc-col-header-cell.fc-day{
        padding: var(--spaceSM);
    }
    table.fc-scrollgrid.fc-scrollgrid-liquid{
        border: 1.25px solid var(--secondaryColor)!important;
        border-radius: var(--roundedSM); /*Fix this*/
    }
    th.fc-col-header-cell.fc-day a{
        font-size: var(--textXLG);
        color: var(--whiteColor) !important;
    }
    td.fc-daygrid-day.fc-day{
        border: 1.25px solid var(--secondaryColor)!important;
        height: 40px !important;
    }
    .fc-daygrid-event, .fc-timegrid-event{
        background: var(--warningColor) !important;
        white-space: normal !important;
        margin: 0 12px 12px 16px !important;
        font-weight: 500;
        border-radius: 0 var(--roundedMini) var(--roundedMini) 0;
        border-left: 2.5px solid var(--secondaryColor);
        position: relative;
        cursor: pointer;
        border: 1px solid var(--secondaryColor);
        -webkit-transition: all 0.4s !important;
        -o-transition: all 0.4s !important;
        transition: all 0.4s !important;
    }

    .fc-daygrid-event.fc-daygrid-dot-event{
        padding: 6px 0 var(--spaceXSM) 4px !important;
    }
    .fc-daygrid-event.fc-daygrid-block-event{
        padding: 6px 0 var(--spaceXSM) 10px !important;
    }
    .fc-v-event .fc-event-title-container{
        padding: var(--spaceXSM) !important;
    }
    .fc-daygrid-event:last-child, .fc-timegrid-event:last-child {
        padding-bottom: 0;
    }
    .fc-daygrid-event .fc-event-title, .fc-timegrid-event .fc-event-title{
        font-size: var(--textXSM);
        font-weight: 500;
    }

    /* Show More */
    .fc-popover.fc-more-popover{
        border-radius: var(--roundedMini);
        -webkit-transition: all 0.25s;
        -o-transition: all 0.25s;
        transition: all 0.25s;
        background: var(--firstColor);
    }
    .fc-popover.fc-more-popover .fc-popover-title{
        font-weight: 500;
        color: var(--secondaryColor);
    }
    .fc-popover.fc-more-popover .fc-popover-body{
        flex-direction: column;
        height: 60vh;
        z-index: 999;
        overflow-y: scroll;
    }
    .fc-popover-close.fc-icon.fc-icon-x {
        width: 35px !important;
        height: 35px !important;
        padding: calc(var(--spaceMini) + 2px) !important;
        box-shadow: none;
        border: 1.5px solid var(--dangerBG);
        border-radius: var(--roundedSM);
        color: var(--secondaryColor);
        font-weight: 600;
    }
    .fc-daygrid-more-link.fc-more-link{
        color: var(--secondaryColor) !important; 
        float: right !important;
        top: -5px;
        margin-right: var(--spaceXXSM);
        font-size: var(--textMD);
        font-weight: 400;
    }
    .fc-popover-header {
        background: var(--warningColor) !important;
    }

    /* Mobile style */
    @media (max-width: 1024px) {
        #calendar {
            width: 1100px !important;
        }
        #calendar a{
            font-size: var(--textXMD);
        }
        .calendar-holder {
            display: flex; 
            flex-direction: column; 
            max-width: 100vh; 
            overflow-x: scroll;
            padding-top: var(--spaceMD);
        }
        .fc-toolbar-title {
            font-size: var(--textLG) !important;
            position: absolute;
            left: 30px !important;
            top: -10px;
            white-space: nowrap;
        }
        th.fc-col-header-cell.fc-day {
            padding: var(--spaceMini);
        }
        .calendar-tag-holder {
            top: var(--spaceJumbo);
            left: var(--spaceMD);
        }
    }
</style>

<div class="calendar-holder">
    <div id="calendar"></div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: sessionStorage.getItem('locale'),
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'title',
                right: 'prev,next today',
                center: null,
            },
            selectable: true,
            navLinks: true, 
            eventLimit: true,
            dayMaxEvents: 4,
            events: [],
            eventClick: function(info) {
                window.location.href = "/trip?locate=" + info.event.groupId;
            },
        });
        calendar.render()

        const get_calendar = () => {
            $.ajax({
                url: `/api/v1/trip/calendar`,
                type: 'GET',
                beforeSend: function (xhr) {
                    Swal.showLoading()
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.close()
                    const data = response.data
                    let events = []
                    data.forEach(el => {
                        events.push({
                            groupId: el.id,
                            title: `(${el.vehicle_plate_number}) ${el.trip_location_name}`,
                            start: getDateToContext(el.created_at, 'calendar'),
                            end: getDateToContext(el.created_at, 'calendar')
                        })
                    });
                    calendar.addEventSource(events)
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    if(response.status != 404){
                        generate_api_error(response, true)
                    }
                }
            });
        };

        get_calendar()
    });
</script>

