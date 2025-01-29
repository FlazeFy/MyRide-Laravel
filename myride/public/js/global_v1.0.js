const getUUID = () => {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
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