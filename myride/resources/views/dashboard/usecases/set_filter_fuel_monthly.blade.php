<div class="d-inline-block">
    <form action="/dashboard/toogle_view_stats_fuel" method="POST" id="toogle_view_stats_fuel_select">
        @csrf
        <label>Chart Type</label>
        <select class="form-select" id="toogle_view_stats_fuel" name="toogle_view_stats_fuel" onchange="this.form.submit()">
            @php($selected = session()->get('toogle_total_stats_fuel'))
            <option value="fuel_volume" <?php if($selected == 'fuel_volume'){ echo 'selected'; }?>>Fuel Volume</option>
            <option value="fuel_price_total" <?php if($selected == 'fuel_price_total'){ echo 'selected'; }?>>Fuel Price Total</option>
        </select>
    </form>
</div>