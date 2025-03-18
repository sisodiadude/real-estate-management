function getCurrentLocation(successCallback, errorCallback) {
    successCallback({ latitude: 28.7041, longitude: 77.1025 });
    return;
    if (!navigator.geolocation) {
        errorCallback("❌ Location services are not supported on your browser. Please update or try a different browser.");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude, accuracy } = position.coords;
            const userAgent = navigator.userAgent.toLowerCase();

            let issueMessage = "";

            if (accuracy > 5000) {
                issueMessage = "⚠️ Location is based on your internet. Enable GPS for accuracy.";
            } else if (accuracy > 1000) {
                issueMessage = "📍 Location may be inaccurate. Move to an open area.";
            } else if (accuracy > 300) {
                if (userAgent.includes("windows") || userAgent.includes("mac")) {
                    issueMessage = "⚠️ Desktop location is less accurate. Use a mobile device.";
                } else {
                    issueMessage = "⚠️ Weak GPS signal. Enable 'High Accuracy Mode'.";
                }
            }

            if (issueMessage) {
                errorCallback(issueMessage);
                return;
            }

            successCallback({ latitude, longitude });
        },
        (error) => {
            let errorMessage = "⚠️ Unable to get location. Please try again.";

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = "❌ Location access is blocked. Enable location in your browser settings.";
                    break;

                case error.POSITION_UNAVAILABLE:
                    errorMessage = "⚠️ Location not available. Check your internet or GPS.";
                    break;

                case error.TIMEOUT:
                    errorMessage = "⏳ Location request timed out. Retrying...";
                    // Retry once after 5 seconds
                    setTimeout(() => getCurrentLocation(successCallback, errorCallback), 5000);
                    return;

                case error.UNKNOWN_ERROR:
                    errorMessage = "❓ An unknown error occurred. Please refresh the page and try again.";
                    break;

                case 3: // Custom handling for specific device/browser issues
                    errorMessage = "📍 Couldn’t get your location. Turn on GPS and try again.";
                    break;

                case 4:
                    errorMessage = "🔒 Location access is restricted. Disable VPN and try again.";
                    break;

                case 5:
                    errorMessage = "🚫 Your device is blocking location. Allow access in settings.";
                    break;

                case 6:
                    errorMessage = "⚙️ Location is disabled in your browser. Please enable it.";
                    break;
            }

            errorCallback(errorMessage);
        },
        {
            enableHighAccuracy: true,
            timeout: 30000,  // Increased timeout to 30s
            maximumAge: 0, // Cache location for 10s to reduce repeated GPS requests
        }
    );
}

function toggleButton(buttonSelector, {
    textSelector,
    oldText,
    newText,
    spinnerSelector = null
}, isDisabled = false) {
    const element = document.querySelector(buttonSelector);
    if (!element) return;

    element.disabled = isDisabled;

    const textElement = element.querySelector(textSelector);
    if (textElement) {
        textElement.textContent = isDisabled ? newText : oldText;
    }

    if (spinnerSelector) {
        const spinner = element.querySelector(spinnerSelector);
        if (spinner) {
            spinner.classList.toggle("d-none", !isDisabled);
        }
    }

    if (isDisabled) {
        // Clear previous validation errors
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        document.querySelectorAll(".invalid-feedback").forEach(el => el.textContent = "");
    }
}
