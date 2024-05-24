@extends('layouts.main_layout')

@section('content')
    <div class="d-block mx-auto p-3" style="max-width: 1080px;">
        <button class="btn btn-nav-page" onclick="window.location.href='/garage'"><i class="fa-solid fa-house"></i> Back to Garage</button><br>
        @include('garage.edit.usecases.put_vehicle_data')
        @include('garage.edit.usecases.put_vehicle_doc')
    </div>
@endsection

<script src='https://www.gstatic.com/firebasejs/6.0.2/firebase.js'></script>
<script>
    const firebaseConfig = {
        apiKey: "AIzaSyAziQMCG6NEKuLhFp9AyzavVPRMdJwT5uw",
        authDomain: "myride-a0077.firebaseapp.com",
        projectId: "myride-a0077",
        storageBucket: "myride-a0077.appspot.com",
        messagingSenderId: "868020179967",
        appId: "1:868020179967:web:0dccb0551a6faeeb810dca",
        measurementId: "G-GTZV92C8MK"
    }
    firebase.initializeApp(firebaseConfig)
</script>