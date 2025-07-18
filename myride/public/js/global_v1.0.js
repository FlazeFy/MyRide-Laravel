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
    } else {
        Swal.fire({
            title: "Oops!",
            text: response.responseJSON?.message || "Something went wrong",
            icon: "error"
        });
    }
}