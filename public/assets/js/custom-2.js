function getCurrentLocation(successCallback, errorCallback) {
    if (!navigator.geolocation) {
        errorCallback("❌ Geolocation is not supported by your browser. Please update or use a different browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude } = position.coords;
            successCallback({ latitude, longitude });
        },
        (error) => {
            let errorMessage = "⚠️ An unexpected error occurred while retrieving location data.";

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = "❌ Location access has been denied. Please enable location services in your browser settings and try again.";
                    break;

                case error.POSITION_UNAVAILABLE:
                    errorMessage = "⚠️ Unable to determine location due to network or GPS issues. Please ensure you have a stable internet connection and try again.";
                    break;

                case error.TIMEOUT:
                    errorMessage = "⏳ The request to retrieve location information timed out. Retrying automatically...";
                    // Retry once after 5 seconds
                    setTimeout(() => getCurrentLocation(successCallback, errorCallback), 5000);
                    return;

                case error.UNKNOWN_ERROR:
                    errorMessage = "❓ An unknown error occurred. Please refresh the page and try again.";
                    break;

                case 3: // Custom handling for specific device/browser issues
                    errorMessage = "📍 Your device could not fetch location data. Ensure that GPS and Wi-Fi are enabled.";
                    break;

                case 4: // Handling for restricted location access (corporate networks, VPNs)
                    errorMessage = "🔒 Location services are restricted by your network or VPN. Try disabling VPN and refreshing the page.";
                    break;

                case 5: // Location blocked by system settings
                    errorMessage = "🚫 Your device settings are preventing location access. Enable location permissions for this site in your system settings.";
                    break;

                case 6: // Location services disabled in browser
                    errorMessage = "⚙️ Your browser’s location services are disabled. Please allow location access in your browser settings.";
                    break;
            }

            errorCallback(errorMessage);
        },
        {
            enableHighAccuracy: true,
            timeout: 30000,  // Increased timeout to 30s
            maximumAge: 10000, // Cache location for 10s to reduce repeated GPS requests
        }
    );
}
