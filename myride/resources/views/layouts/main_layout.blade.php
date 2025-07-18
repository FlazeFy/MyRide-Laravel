<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Ride</title>
    <link rel="icon" type="image/png" href="{{asset('assets/logo_nocap.png')}}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/328b2b4f87.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- CSS Collection -->
    <link rel="stylesheet" href="{{ asset('/css/global_v1.0.css') }}"/>

    <!-- JS Collection -->
    <script src="{{ asset('/js/global_v1.0.js')}}"></script>
    <script src="{{ asset('/js/math_v1.0.js')}}"></script>
    <script src="{{ asset('/js/template_v1.0.js')}}"></script>

    <!-- Jquery -->
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <!-- Swal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php 
        // Datatable CDN
        $route = Route::currentRouteName();
        if($route == 'clean'){
            echo "
                <!-- Jquery DataTables -->
                <script type='text/javascript' language='javascript' src='https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js'></script>
            
                <!-- Bootstrap dataTables Javascript -->
                <script type='text/javascript' language='javascript' src='https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js'></script>
            ";
        } else if (strpos($route, 'stats') !== false || strpos($route, 'detail') !== false) {
            echo "
                <!--Apex Chart-->
                <script src='https://cdn.jsdelivr.net/npm/apexcharts'></script>
            ";
        } else if($route == "edit_garage"){
            echo "
                <link rel=stylesheet' href='"; echo asset('/css/attachment_v1.0.css'); echo"'/>
                <script src='"; echo asset('/js/attachment_v1.0.js'); echo"'></script>
            ";
        }
    ?>
</head>
<body>
    @yield('content')
</body>

 <!--Modal-->
@include('others.popup.success')
@include('others.popup.failed')
@include('others.popup.success_mini')

<!-- Others JS -->
<?php 
    $route = Route::currentRouteName();
    if($route == 'add_trip' || $route == 'trip'){
        echo "
            <script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyDXu2ivsJ8Hj6Qg1punir1LR2kY9Q_MSq8&callback=initMap&v=weekly' defer></script>
        ";
    } 
?>

</html>