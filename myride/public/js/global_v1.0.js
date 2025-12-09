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

const generate_api_error = (response, is_list_format) => {
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
    $('input.form-validator').each(function() {
        const $el = $(this);
        const validator = $el.attr('data-validator');

        if (typeof validator === 'undefined' || validator.trim() === '') {
            failedMsg(`Input with id="${$el.attr('id')}" is missing or has empty data-validator`)
        }

        if (validator && validator.trim() === 'must_future') {
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
            });
        }
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