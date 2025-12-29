const listVehicleNameFetchRestTime = 360
const summaryFetchRestTime = 360
const statsFetchRestTime = 120

const getDateToContext = (datetime, type) => {
    if(datetime){
        const result = new Date(datetime);

        if (type == "full") {
            const now = new Date(Date.now());
            const yesterday = new Date();
            const tomorrow = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            if (result.toDateString() === now.toDateString()) {
                return ` ${messages('today_at')} ${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
            } else if (result.toDateString() === yesterday.toDateString()) {
                return ` ${messages('yesterday_at')} ${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
            } else if (result.toDateString() === tomorrow.toDateString()) {
                return ` ${messages('tommorow_at')} ${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
            } else {
                return ` ${result.getFullYear()}/${(result.getMonth() + 1)}/${("0" + result.getDate()).slice(-2)} ${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
            }
        } else if (type == "24h" || type == "12h") {
            return `${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
        } else if (type == "datetime") {
            return ` ${result.getFullYear()}/${(result.getMonth() + 1)}/${("0" + result.getDate()).slice(-2)} ${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
        } else if (type == "date") {
            return `${result.getFullYear()}-${("0" + (result.getMonth() + 1)).slice(-2)}-${("0" + result.getDate()).slice(-2)}`;
        } else if (type == "calendar") {
            const result = new Date(datetime);
            const offsetHours = getUTCHourOffset();
            result.setUTCHours(result.getUTCHours() + offsetHours);
        
            return `${result.getFullYear()}-${("0" + (result.getMonth() + 1)).slice(-2)}-${("0" + result.getDate()).slice(-2)} ${("0" + result.getHours()).slice(-2)}:${("0" + result.getMinutes()).slice(-2)}`;
        }        
    } else {
        return "-"
    }
}

const getUTCHourOffset = () => {
    const offsetMi = new Date().getTimezoneOffset();
    const offsetHr = -offsetMi / 60;
    return offsetHr;
}

const formatDateTimeAPI = (value) => {
    const d = new Date(value)
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    const hours = String(d.getHours()).padStart(2, '0')
    const minutes = String(d.getMinutes()).padStart(2, '0')
    const seconds = String(d.getSeconds()).padStart(2, '0')

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

const getUUID = () => {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
}

const ucEachWord = (val) => {
    const arr = val.split(" ")
    for (var i = 0; i < arr.length; i++) {
        arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1)
    }
    const res = arr.join(" ")

    return res
}

const ucFirst = (val) => {
    if (typeof val !== 'string' || val.length === 0) {
        var res = val
    } else {
        var res = val.charAt(0).toUpperCase() + val.slice(1)
    }

    return res
}

const generatePagination = (items_holder, fetch_callback, total_page, current_page) => {
    let page_element = ''
    for (let i = 1; i <= total_page; i++) {
        page_element += `
            <a class='btn-page ${i === current_page ? 'active' : ''}' href='#' data-page='${i}' title='Open page: ${i}'>${i}</a>
        `
    }

    $(`#pagination-${items_holder}`).remove()
    if ($('#'+items_holder).closest('table').length == 0) {
        $(`<div id='pagination-${items_holder}' class='btn-page-holder'><label>Page</label>${page_element}</div>`).insertAfter(`#${items_holder}`)
    } else {
        $(`<div id='pagination-${items_holder}' class='btn-page-holder'><label>Page</label>${page_element}</div>`).insertAfter($(`#${items_holder}`).closest('table'))
    }
    $(document).off('click', `#pagination-${items_holder} .btn-page`)
    $(document).on('click', `#pagination-${items_holder} .btn-page`, function() {
        const selectedPage = $(this).data('page')
        fetch_callback(selectedPage)
    });

    const table = $(`#${items_holder}`).closest('table')
    if (table.length) {
        table.css('margin-bottom', '0')
    }
}

const generateApiError = (response, is_list_format) => {
    if (response.status === 422) {
        let msg = response.responseJSON.message
        
        if(typeof msg != 'string'){
            const allMsg = Object.values(msg).flat()
            if(is_list_format){
                msg = '<ol>'
                allMsg.forEach((dt) => {
                    msg += `<li>- ${dt.replace('.','')}</li>`
                })
                msg += '</ol>'
            } else {
                msg = allMsg.join(', ').replace('.','')
            }
        }

        Swal.fire({
            title: "Validation Error!",
            html: msg,
            icon: "error"
        });
    } else if(response.status === 404){
        Swal.fire({
            title: "Oops!",
            html: "Data not found",
            icon: "error"
        });
    } else {
        Swal.fire({
            title: "Oops!",
            text: response.responseJSON?.message || "Something went wrong",
            icon: "error"
        });
    }
}

const validateInput = (type, id, max, min) => {
    if(type == "text"){
        const check = $(`#${id}`).val()
        const check_len = check.trim().length
    
        if(check && check_len > 0 && check_len <= max && check_len >= min){
            return true
        } else {
            return false
        }
    }
}

const callModal = (id) => {
    const modal = new bootstrap.Modal(document.getElementById(id))
    modal.show()
}

const buttonSetRoute = () => {
    $(document).on('click','.btn-set-route', function(){
        const coorDestination = $(this).data('trip-destination-coordinate')
        const coorOrigin = $(this).data('trip-origin-coordinate')
        const travelMode = $(this).data('vehicle-type') === 'Motorcycle' ? 'two-wheeler' :'driving'

        const url = `https://www.google.com/maps/dir/?api=1&origin=${coorOrigin}&destination=${coorDestination}&travelmode=${travelMode}`
        window.open(url, '_blank')
    })
}

const checkScreenSize = (width) => {
    if (width < 320) {
        Swal.fire({
            title: "Warning!",
            text: "Your device has a small screen width, so some content may not display properly",
            icon: "warning"
        });
    }
}

const setChecklistHolder = () => {
    const list_checklist = ['is_wash_body', 'is_wash_window', 'is_wash_dashboard', 'is_wash_tires', 'is_wash_trash', 'is_wash_engine', 'is_wash_seat', 'is_wash_carpet', 'is_wash_pillows', 'is_fill_window_washing_water', 'is_wash_hollow']

    $('#checklist-holder').empty()
    list_checklist.forEach(dt => {
        $('#checklist-holder').append(`
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="${dt}">
                    <label class="form-check-label">${ucEachWord(dt.replaceAll('_',' ').replaceAll('is',''))}</label>
                </div>
            </div>
        `)
    });
}

const getMonthYear = () => {
    const date = new Date()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()
    
    return `${month}-${year}`
}

const applyAutoTheme = () => {
    const hour = new Date().getHours()

    if (hour >= 18 || hour < 6) {
        $("body").removeClass("light").addClass("dark")
        localStorage.setItem("theme", "dark")
    } else {
        $("body").removeClass("dark").addClass("light")
        localStorage.setItem("theme", "light")
    }
}

const validatorInput = () => {
    $('.form-validator').each(function() {
        const $el = $(this)
        const validator = $el.attr('data-validator')

        if(typeof validator === 'undefined' || validator.trim() === '') {
            failedMsg(`Input with id="${$el.attr('id')}" is missing or has empty data-validator`)
        }

        if(validator && validator.trim() === 'must_future') {
            $el.on('blur', function() {
                const type = $el.attr('type')
                const val = $el.val()

                if(!val) return
                let chosenDate, now = new Date()

                if(type === 'datetime-local') {
                    chosenDate = new Date(val)
                } else if (type === 'date') {
                    chosenDate = new Date(val + 'T00:00:00')
                }

                if(chosenDate && chosenDate <= now) {
                    failedMsg('Please choose a future date/time')
                    $(this).val(null)
                }
            })
        }

        if(validator && validator.trim() === 'must_coordinate') {
            $el.on('blur', function() {
                const val = $(this).val()
                if (!val) return

                const parts = val.split(',')
                if (parts.length !== 2) {
                    failedMsg('Coordinate must be in "lat,lon" format')
                    $(this).val(null)
                    return
                }

                const lat = parseFloat(parts[0].trim())
                const lon = parseFloat(parts[1].trim())

                const validLat = !isNaN(lat) && lat >= -90 && lat <= 90
                const validLon = !isNaN(lon) && lon >= -180 && lon <= 180

                if (!validLat || !validLon) {
                    failedMsg('Invalid coordinate. Latitude must be between -90 and 90, longitude between -180 and 180')
                    $(this).val(null)
                }
            })
        }

        if (validator && validator.trim() === 'tidy_up_comma') {
            $el.on('blur', function() {
                const val = $(this).val().trim()
                if (val !== '') {
                    const textComma = val.split(/,\s+and\s+|,\s+|\s+and\s+/).map(dt => dt.trim().toLowerCase()).filter(dt => dt !== '')
                    let cleanTextComma = ''
            
                    if (textComma.length > 1) {
                        cleanTextComma = textComma.slice(0, -1).map(dt => ucEachWord(dt)).join(', ') + `, and ${ucEachWord(textComma[textComma.length - 1])}`
                    } else if (textComma.length === 1) {
                        cleanTextComma = ucEachWord(textComma[0])
                    }
            
                    $(this).val(cleanTextComma)
                }
            })
        }        
    });
}

const resetLocalStorage = (keys) => {
    keys.forEach(dt => {
        localStorage.removeItem(dt)
        localStorage.removeItem(`last-hit-${dt}`) 
    });
}

$(document).ready(() => {
    let width = $(window).width()

    buttonSetRoute()
    checkScreenSize(width)
    validatorInput()

    $(window).on('resize', function() {
        width = $(window).width()
        checkScreenSize(width)
    });
})