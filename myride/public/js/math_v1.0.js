const calculate_distance = (lat1, lon1, lat2, lon2, unit = 'km') => {
    const toRad = (deg) => (deg * Math.PI) / 180

    let theta = lon1 - lon2
    let distance = Math.sin(toRad(lat1)) * Math.sin(toRad(lat2)) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.cos(toRad(theta))
    
    distance = Math.acos(distance)
    distance = (distance * 180) / Math.PI
    distance = distance * 60 * 1.1515 

    if (unit === 'km') {
        distance *= 1.609344
    }

    return distance.toFixed(2)
}

const number_format = (number, decimals, dec_point, thousands_sep) => {
    number = number.toFixed(decimals);

    var nstr = number.split('.');
    var x1 = nstr[0];
    var x2 = nstr.length > 1 ? dec_point + nstr[1] : '';
    
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');
    }
    
    return x1 + x2;
}