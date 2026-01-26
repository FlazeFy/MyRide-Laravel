<style>
    .step {
        position: sticky;
        top: var(--spaceMD);
        background: var(--firstColor);
        padding: var(--spaceLG);
        z-index: 999;
        width: 100%;
        border-radius: var(--roundedMD);
        display: block;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    }
</style>

<div class="step mb-4">
    <div class="mb-3">
        <a class="btn btn-danger py-1" id="back-button" data-current-step="tnc"><i class="fa-solid fa-arrow-left mx-1"></i> Back to Login</a>
    </div>
    <div class="progress mb-2">
        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="title">Hello There!</div>
    <div class="caption">Do you aggree with our terms & condition?</div>
</div>

<script>
    $( document ).ready(function() {
        $(document).on('click','#back-button',function(){
            const currentStep = $(this).data('current-step')
            
            switch (currentStep) {
                case "tnc":
                    window.location.href = '/login'
                    break
                case "profile":
                    
                    break
                case "token":
                    Swal.fire("Oops!", "You must validated your account first!", "error")
                    break
                case "service":
                    
                    break
                default:
                    break
            }
        })
    });
</script>