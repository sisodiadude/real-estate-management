function getCurrentLocation(successCallback, errorCallback) {
    if (!navigator.geolocation) {
        errorCallback("Geolocation is not supported by your browser. Please update or use a different browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            // Success callback will alert the location
            successCallback({ latitude, longitude });
            // alert(`Location fetched successfully! Latitude: ${latitude}, Longitude: ${longitude}`);
        },
        (error) => {
            let errorMessage = "An unexpected error occurred while retrieving location data.";
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = "Location access has been denied. Please enable location services and try again.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = "Unable to determine location. Please check your network or try again later.";
                    break;
                case error.TIMEOUT:
                    errorMessage = "The request to retrieve location information timed out. Please try again.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMessage = "An unknown error occurred while fetching location data. Please refresh and try again.";
                    break;
            }
            // Error callback will alert the error message
            errorCallback(errorMessage);
            alert(errorMessage);
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}
