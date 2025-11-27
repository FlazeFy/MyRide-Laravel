<div style="margin: 10vh 0; max-width: 1080px;" class="d-block mx-auto" id="services_section">
    <br>
    <h2 class="fw-bold" style="font-size:50px;">Most Question They Ask</h2>
    <div class="row mt-4 accordion" id="faq_holder"></div>
</div>

<script>
    const get_faq = () => {
        $.ajax({
            url: `/api/v1/question/faq`,
            type: 'GET',
            beforeSend: function (xhr) {
                Swal.showLoading()
                xhr.setRequestHeader("Accept", "application/json")  
            },
            success: function(response) {
                Swal.close()
                const data = response.data

                $('#faq_holder').empty()

                data.forEach((dt,idx) => {
                    $('#faq_holder').append(`
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 mx-auto">
                            <div class="container-landing bg-warning">
                                <button class="btn btn-primary pt-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFAQ_${idx}" aria-expanded="false" aria-controls="collapseExample">
                                    <h4>${dt.faq_question}</h4>
                                </button>
                                <div class="collapse p-3 mt-3" id="collapseFAQ_${idx}" data-bs-parent="#faq_holder">${dt.faq_answer}</div>
                            </div>
                        </div>
                    `)
                });
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                generate_api_error(response, true)
            }
        });
    }
    get_faq()
</script>