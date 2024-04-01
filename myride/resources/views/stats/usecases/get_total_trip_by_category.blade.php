@php($ctx = 'total_trip_by_category')
@include('others.pie_chart', ['data'=>$total_trip_by_category, 'ctx'=>$ctx])