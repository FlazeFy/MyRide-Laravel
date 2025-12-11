<div class="d-flex flex-wrap gap-2 align-items-center mb-3 justify-content-between">
    <label class="mb-0">Main Image</label>
    <div class="d-flex gap-2" id="vehicle_image_button-holder">
        <a class="btn btn-primary py-1" id="add_image-button"><i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span></a>
    </div>
</div>
<div id="vehicle_img-holder"></div>

<script>
    $(document).ready(function() {
        $(document).on('click', '#clear_image-button', function(){
            $('#clear_image-button').remove()
            $('#save_image-button').remove()
            $('#add_image-button').removeClass('d-none').html(`<i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> ${vehicle_img_url ? 'Change' : 'Add'} Image`)
            $('#vehicle_img-holder').empty()
            if(vehicle_img_url){
                $('#vehicle_img-holder').html(`<img class="img img-fluid" src="${vehicle_img_url}" alt="${vehicle_img_url}"/>`)
            } else {
                template_alert_container('vehicle_img-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)
            }
            $('#remove_image-button').removeClass('d-none') 
            $("#vehicle_img-holder img").removeClass('d-none')
        })

        $(document).on('click', '#add_image-button', function () {
            $("#vehicle_img-holder .alert-container").remove()

            if ($("#vehicle_img-holder .vehicle-image-holder").length > 0) {
                Swal.fire({
                    title: "Error!",
                    text: "You can only add one image",
                    icon: "error"
                })
                return
            }

            $('#add_image-button').addClass('d-none')
            if($('#clear_image-button').length === 0){
                $('#vehicle_image_button-holder').prepend(`
                    <a class="btn btn-danger py-1" id="clear_image-button">
                        <i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span>
                    </a>
                `)
            }
            $('#remove_image-button').removeClass('d-none')

            $("#vehicle_img-holder").append(`
                <div class="container-fluid vehicle-image-holder mt-2">
                    <input type="file" id="vehicle_image" accept="image/jpeg,image/png,image/gif"><br>
                    <img id="image-preview" class="mt-2 d-none" style="max-width: 200px;">
                </div>
            `)
            $("#vehicle_img-holder img").addClass('d-none')
        })

        $(document).on('change', '#vehicle_image', function(e) {
            const file = e.target.files[0]
            if (!file) return

            const maxSize = 5 * 1024 * 1024
            if (file.size > maxSize) {
                failedMsg('File too large. Maximum file size is 5 MB')
                $(this).val('')
                $('#image-preview').addClass('d-none').attr('src', '')
                return
            }

            const reader = new FileReader()
            reader.onload = function (event) {
                $('#image-preview').attr('src', event.target.result).removeClass('d-none')
            }
            reader.readAsDataURL(file)

            $('#remove_image-button').addClass('d-none')
            if($('#save_image-button').length === 0){
                $('#vehicle_image_button-holder').append(`
                    <a class="btn btn-success py-1" id="save_image-button">
                        <i class="fa-solid fa-floppy-disk"></i> <span class="d-none d-md-inline"> Save Image</span>
                    </a>
                `)
            }
        })

        $(document).on('click', '#remove_image-button', function () {
            const id = '<?= $id ?>'

            Swal.fire({
                title: "Are you sure?",
                html: `Do you want to remove the vehicle image?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No, cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/v1/vehicle/image/${id}`,
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        beforeSend: function (xhr) {
                            Swal.showLoading()
                            xhr.setRequestHeader("Accept", "application/json")
                            xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                        },
                        success: function(response) {
                            Swal.fire("Success!", "Driver has successfully assigned", "success").then(() => window.location.href=`/garage/detail/${id}` )
                        },
                        error: function(response, jqXHR, textStatus, errorThrown) {
                            generate_api_error(response, true)
                        }
                    });
                }
            });
        })

        $(document).on('click', '#save_image-button', function () {
            const id = '<?= $id ?>'
            const fd = new FormData()
            
            const img = $("#vehicle_image")[0] ? $("#vehicle_image")[0].files[0] : null
            fd.append("vehicle_image", img ? img : null)

            $.ajax({
                url: `/api/v1/vehicle/image/${id}`,
                type: 'POST', 
                processData: false,
                contentType: false,
                data: fd,
                beforeSend: function (xhr) {
                    Swal.showLoading()
                    xhr.setRequestHeader("Accept", "application/json")
                    xhr.setRequestHeader("Authorization", `Bearer ${token}`)
                },
                success: function(response) {
                    Swal.fire("Success!", response.message, "success").then(() => window.location.href=`/garage/detail/${id}` )
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    generate_api_error(response, true)
                }
            });
        })
    })
</script>