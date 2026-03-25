<style>
    #partner-holder .container {
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }
    #partner-holder .container:nth-child(2), #partner-holder .container:nth-child(3) {
        transform: scale(0.95);
    }
    #partner-holder .container:nth-child(1) {
        transform: scale(1);
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.8), 0 0 30px rgba(255, 193, 7, 0.7), 0 0 40px rgba(255, 193, 7, 0.6);
        margin-bottom: var(--spaceJumbo) !important;
        margin-top: var(--spaceLG) !important;
    }
    #partner-holder .container:nth-child(2) {
        box-shadow: 0 0 17.5px rgba(192, 192, 192, 0.75), 0 0 27.5px rgba(192, 192, 192, 0.65), 0 0 37.5px rgba(192, 192, 192, 0.55);
    }
    #partner-holder .container:nth-child(3) {
        box-shadow: 0 0 15px rgba(205, 127, 50, 0.7), 0 0 25px rgba(205, 127, 50, 0.6), 0 0 35px rgba(205, 127, 50, 0.5);
    }
</style>

<div>
    <img src="{{asset('assets/partner.png')}}" alt='partner.png' class="img img-fluid w-100 mb-3" style="max-width: 420px;">
    <h2>Your Adventure Partners</h2>
    <div id="partner-holder">
        <div class="container text-start">
            <h4>#1st Jhon Doe</h4>
            <div class="d-flex mb-2">
                <div class="chip bg-danger mb-0 ms-0">Favorite Day : Tue</div>
                <div class="chip bg-success mb-0 ms-0">Total Distance : 320 Km</div>
            </div>
            <p class="mb-0 fst-italic">Last trip at 24 May 2026</p>
            <img src="{{asset('assets/1-medal.png')}}" alt='1-medal.png' class="img img-fluid w-100 mb-3" style="max-width: 100px; position: absolute; top: -5px; right: 20px;">
        </div>
        <div class="container text-start">
            <h4>#2nd Jhon Doe</h4>
            <div class="d-flex mb-2">
                <div class="chip bg-danger mb-0 ms-0">Favorite Day : Sun</div>
                <div class="chip bg-success mb-0 ms-0">Total Distance : 320 Km</div>
            </div>
            <p class="mb-0 fst-italic">Last trip at 24 May 2026</p>
        </div>
    </div>
</div>