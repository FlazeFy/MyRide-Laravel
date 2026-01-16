<form action="/dashboard/toogle_view_stats_fuel" method="POST" id="toogle_view_stats_fuel_select">
    @csrf
    <div class="d-flex gap-2 align-items-center mb-4">
        <label class="text-nowrap mb-0">Select View</label>
        <select class="form-select mb-0" id="toogle_view_stats_fuel" name="toogle_view_stats_fuel" onchange="this.form.submit()" style="width:200px;">
            @php($selected = session()->get('toogle_total_stats_fuel'))
            <option value="fuel_volume" <?php if($selected == 'fuel_volume'){ echo 'selected'; }?>>Fuel Volume</option>
            <option value="fuel_price_total" <?php if($selected == 'fuel_price_total'){ echo 'selected'; }?>>Fuel Price Total</option>
        </select>
    </div>
</form>