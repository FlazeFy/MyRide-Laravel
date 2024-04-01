@php($ctx = 'total_vehicle_by_category')
@include('others.pie_chart', ['data'=>$total_vehicle_by_category, 'ctx'=>$ctx])