function getAttachmentInput(index, val){
    $("#attach-input-holder-"+index).html("");
    document.getElementById("attachment_container_"+index).style = "border-left:3.5px solid var(--greyColor); --circle-attach-color-var:var(--greyColor) !important";
    $("#attach-warning-"+index).empty();

    setValue(index);

    attach_list[objIndex]['attach_type'] = null;
    attach_list[objIndex]['attach_name'] = null;
    attach_list[objIndex]['attach_url'] = null;
    attach_list[objIndex]['is_add_more'] = false;

    //Allowed type
    if(val == 'attachment_image'){
        var allowed = 'accept="image/*"';
    } else if(val == 'attachment_video'){
        var allowed = 'accept="video/*"';
    } else if(val == 'attachment_doc'){
        var allowed = 'accept="application/pdf"';
    }

    if (val === "attachment_url") {
        $("#preview_att_" + index).empty();
        $("#attach-input-holder-" + index).append(`
            <h6 class="mt-1">Attach URL</h6>
            <input type="text" id="attach_url_${index}" name="attach_url" class="form-control m-2" onblur="setValue('${index}', true)" required>
            <h6 class="mt-1">Attach Name</h6>
            <input type="text" id="attach_name_${index}" name="attach_name" class="form-control m-2" onblur="setValue('${index}', true)">
        `);
    } else {
        if (!$("#preview_att_" + index).has("*").length) {
            $("#preview_att_" + index).html(`
                <a class="btn btn-icon-preview" title="Preview Attachment" data-bs-toggle="collapse" href="#collapsePreview-${index}">
                <i class="fa-regular fa-eye-slash"></i>
                </a>
            `);
        }
    
        $("#attach-input-holder-" + index).append(`
            <input type="file" id="attach_url_${index}" name="attach_input" class="form-control m-2" ${allowed} onblur="setValue('${index}', true)">
            <input type="text" id="attach_url_holder_${index}" hidden required>
            <h6 class="mt-1">Attach Name</h6>
            <input type="text" id="attach_name_${index}" name="attach_name" class="form-control m-2" onblur="setValue('${index}', true)">
        `);
    }
}

function cleanAddMoreStatus(list){
    var final = list.map(function(obj) {
        delete obj.is_add_more;
        return obj;
    });

    return final;
}

function removeAttachment(type, list, idx){
    var new_list = list.filter(object => {
        if(type != "attachment_url" && object.id == idx){
            var filePath = object.attach_url;
            if(filePath){
                var storageRef = firebase.storage();
                var desertRef = storageRef.refFromURL(filePath);
                var msg = ""

                desertRef.delete().then(() => {
                    msg = `${messages('removed_att')}`;
                }).catch((error) => {
                    msg = `${messages('failed_removed')}`;
                });
            }
        } 

        $("#attachment_container_"+idx).remove();

        return object.id !== idx;
    });

    return new_list
}

function doErrorAttachment(id, error){
    document.getElementById('attach_type_'+id).disabled = false;
    document.getElementById('attach_url_'+id).value = null;
    document.getElementById('attach_name_'+id).disabled = true;
    document.getElementById('attach-failed-'+id).innerHTML = `File upload is ${error.message}`;
}

function isAddMoreAttachment(list){
    if(list.length != 0){
        let check = true
        list.forEach(e => {
            if(e.attach_url == null || e.attach_url.trim() == "" || e.is_add_more == false){
                check = false
            } 
        });

        return check
    } else {
        return true
    }
}

function getAttCode() {
    let col = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    let code = ''
    for (let i = 0; i < 6; i++) {
        let index = Math.floor(Math.random() * col.length)
        code += col[index]
    }
    return code
}

function isValidURL(urlString){
    try { 
        return Boolean(new URL(urlString))
    }
    catch(e){ 
        return false
    }
}