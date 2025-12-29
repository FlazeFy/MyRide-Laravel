const calculateDistance = (lat1, lon1, lat2, lon2, unit = 'km') => {
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