<div class="d-flex align-items-center justify-content-between">
    <h2 class="mb-0">Document</h2>
    <div class="d-flex flex-wrap gap-2" id="vehicle_document_button-holder">
        <a class="btn btn-primary" id="add_doc-button"><i class="fa-solid fa-file"></i><span class="d-none d-md-inline"> Add Doc</span></a>
    </div>
</div><hr>

<div id="doc_attachment-holder" class="row"></div>

<script>
    templateAlertContainer('doc_attachment-holder', 'no-data', "No document attached", null, '<i class="fa-solid fa-link"></i>', null)

    $(document).ready(function() {
        $(document).on('click', '#clear_doc-button', function(){
            $('#vehicle_document_button-holder').find(this).remove()
            $('#vehicle_document_button-holder').find('#save_doc-button').remove()
            $("#doc_attachment-holder").empty()
            templateAlertContainer('doc_attachment-holder', 'no-data', "No document attached", null, '<i class="fa-solid fa-link"></i>', null)
        })

        $(document).on('click', '#add_doc-button', function () {
            if($('#vehicle_document_button-holder').find('#clear_doc-button').length === 0){
                $('#vehicle_document_button-holder').prepend(`
                    <a class="btn btn-danger" id="clear_doc-button"><i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span></a>
                    <a class="btn btn-success" id="save_doc-button"><i class="fa-solid fa-floppy-disk"></i><span class="d-none d-md-inline"> Save Doc</span></a>
                `)
            }

            if($("#doc_attachment-holder").find('.vehicle_document-holder').length === 0){
                $("#doc_attachment-holder").empty()
            }

            const uid = Date.now()
            $("#doc_attachment-holder").append(`
                <div class="col-md-6 col-sm-12">
                    <div class="container-fluid vehicle_document-holder mt-2">
                        <input type="file" class="vehicle_document form-control" id="vehicle_document_${uid}" accept="image/jpeg,image/png,application/pdf"><br>
                        <div id="doc-preview_${uid}" class="my-2"></div>
                        <label>Caption (Optional)</label>
                        <input class="form-control vehicle_document_caption" type="text">
                    </div>
                </div>
            `)
        })

        $(document).on('change', '.vehicle_document', function(e) {
            const file = e.target.files[0]
            if (!file) return

            const maxSize = 5 * 1024 * 1024
            if (file.size > maxSize) {
                failedMsg('File too large. Maximum file size is 5 MB')
                $(this).val('')
                return
            }

            const inputId = $(this).attr('id')
            const previewId = inputId.replace('vehicle_document', 'doc-preview')

            const previewHolder = $(`#${previewId}`)
            previewHolder.html('')

            const reader = new FileReader()
            const type = file.type

            reader.onload = function(event) {

                if (type === "application/pdf") {
                    previewHolder.html(`
                        <iframe src="${event.target.result}" width="100%" height="400px"></iframe>
                    `)
                } 
                else if (type.startsWith("image/")) {
                    previewHolder.html(`
                        <img src="${event.target.result}" class="img-fluid rounded border" style="max-height: 300px;"/>
                    `)
                } 
                else {
                    failedMsg('Unsupported file format')
                }
            }

            reader.readAsDataURL(file)
        })

        $(document).on('click','#save_doc-button',function(){
            let allFilled = true
            $('.vehicle_document').each(function(){
                if (!this.files || this.files.length === 0) {
                    allFilled = false
                }
            })

            if (!allFilled) {
                failedMsg("All document inputs must be filled before uploading")
                return
            }

            Swal.fire({
                title: "Warning!",
                text: "Are you sure want to upload this document to our apps?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Upload",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    post_document("<?= $id ?>")
                } else if (result.isDismissed) {
                    $('#vehicle_document_button-holder').find('#clear_doc-button').remove()
                    $('#vehicle_document_button-holder').find('#save_doc-button').remove()
                    $("#doc_attachment-holder").empty()
                    templateAlertContainer('doc_attachment-holder', 'no-data', "No document attached", null, '<i class="fa-solid fa-link"></i>', null)
                }
            });
        })

        const post_document = (id) => {
            const fd = new FormData()

            let totalFiles = 0

            $(".vehicle_document").each(function() {
                const files = this.files
                if (!files.length) return

                totalFiles += files.length;
                for (let i = 0; i < files.length; i++) {
                    fd.append("vehicle_document[]", files[i])
                    fd.append("vehicle_document_caption[]", $(this).closest('.vehicle_document-holder').find('.vehicle_document_caption').val())
                }
            });

            if (totalFiles > 10) {
                failedMsg("You can only upload up to 5 at one time")
                return
            }

            $.ajax({
                url: `/api/v1/vehicle/doc/${id}`,
                type: 'POST',
                processData: false,
                contentType: false,
                data: fd,
                beforeSend: function (xhr) {
                    Swal.showLoading()
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function (response) {
                    Swal.close()
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        window.location.href = `/garage/detail/${id}`
                    })
                },
                error: function (response) {
                    Swal.close()
                    if (response.status === 500) {
                        generateApiError(response, true)
                    } else {
                        failedMsg(response.status === 400 ? Object.values(response.responseJSON.message).flat().join('\n') : response.responseJSON.message)
                    }
                }
            })
        }
    })
</script>
