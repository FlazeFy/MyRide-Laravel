<div class="d-flex flex-wrap gap-2 align-items-center mb-3 justify-content-between">
    <label class="mb-0">Image Collection</label>
    <div class="d-flex gap-2" id="vehicle_image_collection_button-holder">
        <a class="btn btn-primary py-1" id="add_image_collection-button"><i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> Add Image</span></a>
    </div>
</div>
<div id="vehicle_img_collection-holder" class="row mx-1"></div>

<script>
    $(document).ready(function() {
        $(document).on('click', '#clear_image_collection-button', function(){
            $('#clear_image_collection-button').remove()
            $('#save_image_collection-button').remove()
            $('#add_image_collection-button').removeClass('d-none').html(`<i class="fa-solid fa-image"></i><span class="d-none d-md-inline"> ${vehicle_img_url ? 'Change' : 'Add'} Image`)
            $('#vehicle_img_collection-holder').empty()
            if(vehicle_img_url){
                $('#vehicle_img_collection-holder').html(`<img class="img img-fluid" src="${vehicle_img_url}" alt="${vehicle_img_url}"/>`)
            } else {
                template_alert_container('vehicle_img_collection-holder', 'no-data', "No image selected", null, '<i class="fa-solid fa-image"></i>', null)
            }
            $('#remove_image_collection-button').removeClass('d-none') 
            $("#vehicle_img_collection-holder img").removeClass('d-none')
        })

        $(document).on('click', '#add_image_collection-button', function () {
            $("#vehicle_img_collection-holder .alert-container").remove()

            if ($("#vehicle_img_collection-holder .vehicle-image-holder").length > 9) {
                Swal.fire({
                    title: "Error!",
                    text: "You can only add one image",
                    icon: "error"
                })
                return
            }

            if($('#clear_image_collection-button').length === 0){
                $('#vehicle_image_collection_button-holder').prepend(`
                    <a class="btn btn-danger py-1" id="clear_image_collection-button">
                        <i class="fa-solid fa-circle-xmark"></i><span class="d-none d-md-inline"> Clear</span>
                    </a>
                `)
            }
            $('#remove_image_collection-button').removeClass('d-none')

            $("#vehicle_img_collection-holder").append(`
                <div class="col-md-6 col-sm-12">
                    <div class="container-fluid vehicle-image-holder mt-2">
                        <input type="file" class="vehicle_other_images" accept="image/jpeg,image/png,image/gif"><br>
                        <img class="image-preview mt-2 d-none" style="max-width: 200px;">
                    </div>
                </div>
            `)
        })

        $(document).on('change', '.vehicle_other_images', function(e) {
            const file = e.target.files[0]
            const $preview = $(this).siblings('.image-preview')

            if (!file) return
            const maxSize = 5 * 1024 * 1024
            if (file.size > maxSize) {
                failedMsg('File too large. Maximum file size is 5 MB')
                $(this).val('')
                $preview.addClass('d-none').attr('src', '')
                return
            }

            const reader = new FileReader()
            reader.onload = function (event) {
                $preview.attr('src', event.target.result).removeClass('d-none')
            }
            reader.readAsDataURL(file)

            $('#remove_image_collection-button').addClass('d-none')
            if($('#save_image_collection-button').length === 0){
                $('#vehicle_image_collection_button-holder').append(`
                    <a class="btn btn-success py-1" id="save_image_collection-button">
                        <i class="fa-solid fa-floppy-disk"></i><span class="d-none d-md-inline"> Save Image</span>
                    </a>
                `)
            }
        })

        $(document).on('click', '#remove_image_collection-button', function () {
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
                        url: `/api/v1/vehicle/image_collection/${id}`,
                        type: 'POST',
                        processData: false,
                        contentType: false,
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
                }
            });
        })

        $(document).on('click', '#save_image_collection-button', function () {
            const id = '<?= $id ?>'
            const fd = new FormData()
            
            let totalFiles = 0
            $(".vehicle_other_images").each(function() {
                const files = this.files
                if (!files.length) return

                totalFiles += files.length;
                for (let i = 0; i < files.length; i++) {
                    fd.append("vehicle_other_img_url[]", files[i])
                }
            });

            if (totalFiles > 10) {
                failedMsg("You can only upload up to 10 other images")
                return
            }

            $.ajax({
                url: `/api/v1/vehicle/image_collection/${id}`,
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