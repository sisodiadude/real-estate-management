<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>Omah - Real Estate Admin Dashboard Template</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="DexignZone">
    <meta name="robots" content="index, follow">

    <meta name="keywords"
        content="admin, dashboard, admin dashboard, admin template, template, admin panel, administration, analytics, bootstrap, hospital admin, modern, property, real estate, responsive, creative, retina ready, modern Dashboard">
    <meta name="description"
        content="Your Ultimate Real Estate Admin Dashboard Template. Streamline property management, analyze market trends, and boost productivity with our intuitive and feature-rich solution. Elevate your real estate business today!">

    <meta property="og:title" content="Omah - Real Estate Admin Dashboard Template">
    <meta property="og:description"
        content="Your Ultimate Real Estate Admin Dashboard Template. Streamline property management, analyze market trends, and boost productivity with our intuitive and feature-rich solution. Elevate your real estate business today!">
    <meta property="og:image" content="https://omah.dexignzone.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">

    <meta name="twitter:title" content="Omah - Real Estate Admin Dashboard Template">
    <meta name="twitter:description"
        content="Your Ultimate Real Estate Admin Dashboard Template. Streamline property management, analyze market trends, and boost productivity with our intuitive and feature-rich solution. Elevate your real estate business today!">
    <meta name="twitter:image" content="https://omah.dexignzone.com/xhtml/social-image.png">
    <meta name="twitter:card" content="summary_large_image">

    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/login-custom.css') }}" rel="stylesheet">
</head>

<body data-typography="poppins" data-theme-version="light" data-layout="vertical" data-nav-headerbg="color_1"
    data-headerbg="color_1" data-sidebar-style="compact" data-sibebarbg="color_1" data-primary="color_1"
    data-sidebar-position="fixed" data-header-position="fixed" data-container="wide" direction="ltr">
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root">
        <div class="vh-100">
            <div class="authincation d-flex flex-column flex-lg-row flex-column-fluid">
                <div class="login-aside text-center  d-flex flex-column flex-row-auto">
                    <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
                        <div class="text-center mb-4 pt-5">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAI8AAAAnCAMAAAAB8usiAAAArlBMVEUAAAAQFCQQFCQQESI7S7gQFCURFSUREyUQFCUQEyUQECAREyU7TLgQFCU4Tbc7TLk6TLc7S7g6TLc5S7hATK87TLkQEyMQFCQ7TLcQEyM7TrlAUL/29vb29vb29vb09PQRFCU6S7gSFCQ8Src7TLf29vY6S7gREyU6SrY6SrcQFSUQECX39/fv7+8QFCYQEyY7TbcRFCU7TLj29vakrNtSYcCwtt+wtt5ebMRda8ThXuroAAAAMXRSTlMAgEAf38Df32CfEO+/vyDvwM+AMBDPUHCgkF8Q79/AYM+Qj1BAz7CvkGAwMCAgsLBwzlx8wgAAA0hJREFUWMPN12l34iAUBuAbMM02iRNjE+24b52xq1G7/P8/NnCDgMR0mHNa6/uhIsrhgUvSCHq6nbQs014Cl5F5u8S053AJuSllbuD7g5x2b9W6DBBy0i5Acn0JoLngsPy6AFDWFpzLAEnOZYAURwe1M/jihJSnmWMLCteE0E/wkA0PaeaMR4PtYDT+EHQ3u9rweAH5Ek/COS3OeVhuqwwfGkHhbKNyS7/A0+KchHOGjGKAUjgO9TZ6ovzTPYsDB5AjsgQBWpgcI+SzPVM2KXLG3LHbl28vvDHmlWQf/QY9ghNNXDcSTSxZ7rMQ5i0cJ6eisr7j+PRo+iKY+VR9W3kIHxcCzw+2PdgYMcVrybNjrZEo5Qq0OBXBwZF+daxj3sZmQGP2FxtME1TtmEqNWMwsxK+5ykOqcZ5z5BkwxTt69qw1EJ6f9WqtD28jsTzhiWUxPaoKG63lWkQmhgflGEf3bFnKKrx5wuPLQeZ79KggTYJCrKj+sfIY8Y392SPnrWF/bnEMBZkIl2t6zOACPCuPZ5yfHXpe8PzUPZNqepVqp6UncnJSMCMmyIk4YR5uZYXI71in6XFJeCfKuVYevL5e38v9Dq+vmkfM+lg73/TgIXj71jYlrPpD3FpZ6tjwXGFvvyqY8sByqzIE6THKEzR6XMDEWlWLQztSEwM1PD6gXYiV536oOPd1z/Gs2prk9a51eoAhBw++9vW6u+b9UPcYoCVydI9aeVTrMDzOCQ89ujJjO4/+//2kx5HD9Y1//LdH7Y+9x4zpkcO98Pj69208eH5iuQxbT/aHJTvpUUd1QqsDGIirw8YTayfXsfUkrM2zSho8RNy1gpzkTiRuqVaeQtwFKNDZxtbTKkXSukfOZaQPVp4Q9VVsPfNSZmF4GkFuaOeB4v89Uw5JkgyfepTHAJm7Y+fRB155dp4egwALe+k1eoAGaokErD0K5NL4I8+q5mmb+2OK/L7rPgVFCDKFw5ILA28X4qsOT3gY9+S6fQLg805ffkwBg20in1eV5xmfp+Xz6pmzwB9fuge6UL2krGMO5w7/FdGadhL0dFSm+LsMzp6sXfJco8dIO4HzJ6vugzCtcVoZfEeS55RVChJ0qaSdLpw9fwEu0y2ZxEy1VAAAAABJRU5ErkJggg=="
                                alt="" width="200">
                        </div>
                        <h3 class="mb-2">Welcome back !</h3>
                        <p>User Experience &amp;
                            Interface Design <br>Strategy SaaS Solutions
                        </p>
                    </div>
                    <div class="aside-image"
                        style="background-image: url('{{ asset('assets/images/login/banner.png') }}');">
                    </div>

                </div>
                <div
                    class="container flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
                    <div class="d-flex justify-content-center h-100 align-items-center">
                        <div class="authincation-content style-2">
                            <div class="row no-gutters">
                                <div class="col-xl-12 tab-content">
                                    <div id="sign-in" class="auth-form form-validation">
                                        @if (session('errorAlert'))
                                            <div class="alert alert-danger">
                                                {{ session('errorAlert') }}
                                            </div>
                                        @endif

                                        @php
                                            // Fetch the cookie value and decode it
                                            $rememberedCredentials = json_decode(
                                                Cookie::get('admin_credentials'),
                                                true,
                                            );
                                            $remembered = isset($rememberedCredentials['username']); // Check if there are stored credentials
                                        @endphp

                                        <form id="login-form" class="form-validate" novalidate>
                                            @csrf
                                            <h3 class="text-center mb-4 text-black">Sign in to your account</h3>

                                            <div class="form-group mb-3">
                                                <label class="mb-1"><strong>Email, Mobile, or
                                                        Username</strong></label>
                                                <input type="text" class="form-control" name="username"
                                                    placeholder="Enter your email, mobile, or username"
                                                    value="{{ old('username', $rememberedCredentials['username'] ?? '') }}">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="mb-1"><strong>Password</strong></label>
                                                <input type="password" class="form-control" name="password"
                                                    placeholder="Enter your secure password"
                                                    value="{{ $rememberedCredentials['password'] ?? '' }}">
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                                <div class="form-group mb-3">
                                                    <div class="custom-control custom-checkbox ms-1">
                                                        <input type="checkbox" id="remember_my_choice"
                                                            class="form-check-input" name="remember_my_choice"
                                                            @if ($remembered) checked @endif>
                                                        <label class="form-check-label"
                                                            for="remember_my_choice">Remember my preference</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center form-group mb-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                    id="login-submit-btn">
                                                    <span
                                                        class="spinner-border spinner-login spinner-border-sm me-2 d-none"
                                                        role="status" aria-hidden="true"></span>
                                                    Sign In
                                                </button>
                                            </div>
                                        </form>

                                        <div class="forget-password mt-3">
                                            <p>Forgot your password?
                                                <a href="javascript:void(0);"
                                                    class="text-primary forgot-password-link">
                                                    Reset it here
                                                </a>
                                            </p>
                                        </div>

                                        <!-- Two Factor Authentication Modal -->
                                        <div class="modal fade" id="twoFactorAuthenticationModal" tabindex="-1"
                                            aria-labelledby="twoFactorAuthenticationModalTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-title fw-semibold">Verify Your Code</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <!-- Modal Body -->
                                                    <div class="modal-body">
                                                        <div class="container-fluid bg-body-tertiary p-4 rounded">
                                                            <div class="row justify-content-center">
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <div class="w-100">
                                                                        <div class="text-center p-4">
                                                                            <h4 class="fw-bold">Verify</h4>
                                                                            <p class="text-muted mb-3">A verification
                                                                                code has been sent to your email.</p>

                                                                            <!-- OTP Input Fields -->
                                                                            <div
                                                                                class="d-flex justify-content-center gap-2">
                                                                                <input type="text"
                                                                                    class="otp-input form-control text-center fs-5 fw-semibold border rounded"
                                                                                    maxlength="1"
                                                                                    pattern="[A-Za-z0-9]"
                                                                                    inputmode="numeric"
                                                                                    style="width: 50px; height: 50px;"
                                                                                    autofocus />
                                                                                <input type="text"
                                                                                    class="otp-input form-control text-center fs-5 fw-semibold border rounded"
                                                                                    maxlength="1"
                                                                                    pattern="[A-Za-z0-9]"
                                                                                    inputmode="numeric"
                                                                                    style="width: 50px; height: 50px;"
                                                                                    disabled />
                                                                                <input type="text"
                                                                                    class="otp-input form-control text-center fs-5 fw-semibold border rounded"
                                                                                    maxlength="1"
                                                                                    pattern="[A-Za-z0-9]"
                                                                                    inputmode="numeric"
                                                                                    style="width: 50px; height: 50px;"
                                                                                    disabled />
                                                                                <input type="text"
                                                                                    class="otp-input form-control text-center fs-5 fw-semibold border rounded"
                                                                                    maxlength="1"
                                                                                    pattern="[A-Za-z0-9]"
                                                                                    inputmode="numeric"
                                                                                    style="width: 50px; height: 50px;"
                                                                                    disabled />
                                                                                <input type="text"
                                                                                    class="otp-input form-control text-center fs-5 fw-semibold border rounded"
                                                                                    maxlength="1"
                                                                                    pattern="[A-Za-z0-9]"
                                                                                    inputmode="numeric"
                                                                                    style="width: 50px; height: 50px;"
                                                                                    disabled />
                                                                                <input type="text"
                                                                                    class="otp-input form-control text-center fs-5 fw-semibold border rounded"
                                                                                    maxlength="1"
                                                                                    pattern="[A-Za-z0-9]"
                                                                                    inputmode="numeric"
                                                                                    style="width: 50px; height: 50px;"
                                                                                    disabled />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Footer -->
                                                    <div
                                                        class="modal-footer d-flex flex-column align-items-center border-0">
                                                        <button class="btn btn-primary w-100 py-2 fw-semibold"
                                                            id="two-factor-submit-btn"><span
                                                                class="spinner-border spinner-two-factor spinner-border-sm me-2 d-none"
                                                                role="status"
                                                                aria-hidden="true"></span>Verify</button>
                                                        <p class="text-muted small mt-2 mb-0">
                                                            Didn't receive a code?
                                                            <a href="#"
                                                                class="text-decoration-none fw-medium">Request
                                                                again</a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="PING_IFRAME_FORM_DETECTION" style="display: none;"></span>
    <svg id="SvgjsSvg1273" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1"
        xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
        style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;">
        <defs id="SvgjsDefs1274"></defs>
        <polyline id="SvgjsPolyline1275" points="0,0"></polyline>
        <path id="SvgjsPath1276"
            d="M-1 282.99519938278195L-1 282.99519938278195C-1 282.99519938278195 90.95703411102295 282.99519938278195 90.95703411102295 282.99519938278195C90.95703411102295 282.99519938278195 181.9140682220459 282.99519938278195 181.9140682220459 282.99519938278195C181.9140682220459 282.99519938278195 272.87110233306885 282.99519938278195 272.87110233306885 282.99519938278195C272.87110233306885 282.99519938278195 363.8281364440918 282.99519938278195 363.8281364440918 282.99519938278195C363.8281364440918 282.99519938278195 454.78517055511475 282.99519938278195 454.78517055511475 282.99519938278195C454.78517055511475 282.99519938278195 545.7422046661377 282.99519938278195 545.7422046661377 282.99519938278195C545.7422046661377 282.99519938278195 636.6992387771606 282.99519938278195 636.6992387771606 282.99519938278195C636.6992387771606 282.99519938278195 727.6562728881836 282.99519938278195 727.6562728881836 282.99519938278195C727.6562728881836 282.99519938278195 727.6562728881836 282.99519938278195 727.6562728881836 282.99519938278195C727.6562728881836 282.99519938278195 727.6562728881836 282.99519938278195 727.6562728881836 282.99519938278195 ">
        </path>
    </svg>

    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-2.js') }}"></script>

    <script>
        function validateNoValidateForm(formId, validationConfig) {
            let form = document.getElementById(formId);
            let isValid = true;
            let inputs = form.querySelectorAll(".form-control");

            inputs.forEach(input => {
                let fieldName = input.name;
                let value = input.value.trim();
                let errorMessage = input.nextElementSibling;

                // Reset previous errors
                input.classList.remove("is-invalid");
                errorMessage.textContent = "";

                // Check if this field has validation rules
                if (validationConfig[fieldName]) {
                    let rules = validationConfig[fieldName].rules;
                    let messages = validationConfig[fieldName].messages;
                    let isFieldValid = true; // Assume field is valid initially

                    for (let rule in rules) {
                        let isError = false;

                        if (rule === "required" && !value) {
                            isError = true;
                        } else if (rule === "custom_validation") {
                            let allowedTypes = rules[rule].split(","); // Split types into an array
                            let isValidType = validateCustomField(value, allowedTypes);

                            if (!isValidType) {
                                isError = true;
                            }
                        } else if (rule === "minLength" && value.length < rules[rule]) {
                            isError = true;
                        }

                        if (isError) {
                            input.classList.add("is-invalid");
                            errorMessage.textContent = messages[rule];
                            isFieldValid = false;
                            break; // Stop further validation for this field
                        }
                    }

                    if (!isFieldValid) {
                        isValid = false;
                    }
                }
            });

            return isValid;
        }

        // Custom validation function that checks the allowed types
        function validateCustomField(value, allowedTypes) {
            let validators = {
                "email": value => /^\S+@\S+\.\S+$/.test(value),
                "mobile": value => /^\d{10,15}$/.test(value),
                "username": value => /^[a-zA-Z0-9_]+$/.test(value)
            };

            return allowedTypes.some(type => validators[type] && validators[type](value));
        }

        function toggleButton(button, spinner, disable) {
            if (disable) {
                button.disabled = true;
                spinner.classList.remove("d-none");
            } else {
                button.disabled = false;
                spinner.classList.add("d-none");
            }
        }

        function handleOtpModal(modalId, action) {
            let modalElement = document.getElementById(modalId);
            let modal = new bootstrap.Modal(modalElement);

            if (action === "open") {
                // Reset OTP inputs before showing the modal
                let otpInputs = document.querySelectorAll(".otp-input");
                otpInputs.forEach((input, index) => {
                    input.value = ""; // Clear input
                    input.disabled = index !== 0; // Disable all except the first one
                });

                // Now show the modal
                modal.show();
            } else if (action === "close") {
                modal.hide();
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOM fully loaded and parsed");

            const inputs = document.querySelectorAll("#twoFactorAuthenticationModal .otp-input");
            console.log("OTP inputs selected:", inputs.length);

            const verifyButton = document.querySelector("#two-factor-submit-btn");

            inputs.forEach((input, index) => {
                console.log(`Setting up event listeners for input index: ${index}`);

                input.addEventListener("input", (e) => {
                    console.log(`Input event triggered on index ${index}, value: ${input.value}`);

                    if (e.inputType !== "deleteContentBackward" && input.value) {
                        console.log(`Valid input detected at index ${index}`);

                        if (index < inputs.length - 1) {
                            inputs[index + 1].disabled = false;
                            inputs[index + 1].focus();
                            // console.log(`Enabled and focused next input at index ${index + 1}`);
                        }
                    }

                    console.log(`${index} === ${inputs.length - 1}`);

                    if (e.key === "Enter" || index === inputs.length - 1) {
                        console.log(`Step 1`);
                        checkCompletion();
                    }
                });

                input.addEventListener("keydown", (e) => {
                    console.log(
                        `Keydown event on index ${index}, key: ${e.key}, keyType: ${e.inputType}`
                    );

                    if (e.key === "Backspace") {
                        if (!input.value && index > 0) {
                            console.log(`Backspace detected at index ${index}, moving focus back`);
                            input.value = "";
                            inputs[index - 1].disabled = false;
                            inputs[index - 1].focus();
                            e.preventDefault();
                        } else {
                            console.log(`Backspace at index ${index}, clearing value`);
                            input.value = "";
                            inputs[index - 1].disabled = false;
                            inputs[index - 1].focus();
                        }
                    } else if (/^[a-zA-Z1-9]$/.test(e.key) && input.value) {
                        console.log(`Valid input detected at index ${index}`);

                        if (index < inputs.length - 1) {
                            inputs[index + 1].disabled = false;
                            inputs[index + 1].focus();
                            console.log(`Enabled and focused next input at index ${index + 1}`);
                        }
                    }

                    // Submit on Enter when on the last input and all fields are filled
                    if (e.key === "Enter" && index === inputs.length - 1) {
                        console.log(`Step 2`);
                        checkCompletion();
                    }
                });

                inputs.forEach((input, index) => {
                    input.addEventListener("focus", () => {
                        console.log(`Input at index ${index} focused`);

                        // Disable all inputs except the focused one
                        inputs.forEach((inp, i) => {
                            inp.disabled = i !== index;
                            /*
                            console.log(
                                `Input at index ${i} ${inp.disabled ? "disabled" : "enabled"}`
                            );
                            */
                        });
                    });
                });

            });

            verifyButton.addEventListener("click", function() {
                console.log("Verify button clicked!");
                checkCompletion();
            });

            function checkCompletion() {
                const otpValue = Array.from(inputs).map((inp) => inp.value).join("");
                console.log(`Current OTP value: ${otpValue}`);

                if (otpValue.length === inputs.length) {

                    let twoFactorBtn = document.getElementById("two-factor-submit-btn");
                    let twoFactorSpinner = document.querySelector(".spinner-two-factor");

                    // Disable button & show spinner
                    toggleButton(twoFactorBtn, twoFactorSpinner, true);

                    console.log("OTP fully entered:", otpValue);
                    const username = document.querySelector('input[name="username"]').value;
                    const password = document.querySelector('input[name="password"]').value;
                    const rememberMyChoice = document.getElementById('remember_my_choice').checked;

                    getCurrentLocation(
                        (location) => {
                            console.log("Successfully fetched location:", location);

                            const {
                                latitude,
                                longitude
                            } = location;

                            fetch("{{ route('admin.auth.verify_otp.submit') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-Requested-With": "XMLHttpRequest", // Recognized as an AJAX request
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}" // CSRF token for security
                                    },
                                    body: JSON.stringify({
                                        login_otp: otpValue,
                                        username: username,
                                        password: password,
                                        latitude: latitude,
                                        longitude: longitude,
                                        remember_my_choice: rememberMyChoice
                                    })
                                })
                                .then(response => response.json()) // Convert response to JSON
                                .then(data => {
                                    console.log("Response data:", data);

                                    if (data.status) {
                                        console.log("OTP verified successfully!");
                                        Swal.fire({
                                            title: "Success!",
                                            text: data.message || "OTP Verified! Redirecting...",
                                            icon: "success",
                                            timer: 2000,
                                            showConfirmButton: false
                                        });

                                        setTimeout(() => {
                                            console.log("Redirecting to:", data.redirect_url ||
                                                "/admin");
                                            window.location.href = data.redirect_url ||
                                                "/admin";
                                        }, 2000);
                                    } else {
                                        console.log("Invalid OTP entered.");
                                        Swal.fire({
                                            title: "Error",
                                            text: data.message ||
                                                "The OTP entered is incorrect. Please try again.",
                                            icon: "error"
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error("Error verifying OTP:", error);
                                    Swal.fire({
                                        title: "Error",
                                        text: "Something went wrong. Please try again later.",
                                        icon: "error"
                                    });
                                })
                                .finally(() => {
                                    toggleButton(twoFactorBtn, twoFactorSpinner, false);
                                    console.log("OTP verification request completed.");
                                });
                        },
                        (errorMessage) => {
                            toggleButton(twoFactorBtn, twoFactorSpinner, false);
                            console.error("Error fetching location:", errorMessage);
                            Swal.fire({
                                icon: "error",
                                title: "Location Error",
                                text: errorMessage,
                                confirmButtonText: "OK"
                            });
                        }
                    );
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Please enter the complete OTP.",
                        icon: "error"
                    });
                }
            }

            // Ensure only the first input is enabled initially
            inputs.forEach((inp, index) => {
                inp.disabled = index !== 0;
            });
        });

        document.getElementById("login-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default submission

            let validationConfig = {
                "username": {
                    "rules": {
                        "required": true,
                        "custom_validation": "email,mobile,username" // Can extend with more types later
                    },
                    "messages": {
                        "required": "Please enter your email, mobile number, or username.",
                        "custom_validation": "Enter a valid email, mobile number, or username."
                    }
                },
                "password": {
                    "rules": {
                        "required": true,
                        "minLength": 6
                    },
                    "messages": {
                        "required": "Your password is required to access your account.",
                        "minLength": "Password must be at least 6 characters long."
                    }
                }
            };

            if (validateNoValidateForm("login-form", validationConfig)) {
                let formData = new FormData(this);
                let loginBtn = document.getElementById("login-submit-btn");
                let spinner = document.querySelector(".spinner-login");

                // Disable button & show spinner
                toggleButton(loginBtn, spinner, true);

                getCurrentLocation(
                    (location) => {
                        console.log("Successfully fetched location:", location);

                        const {
                            latitude,
                            longitude
                        } = location;

                        // Get the form data and convert it to an object
                        let formData = Object.fromEntries(new FormData(document.getElementById("login-form")));

                        // Append latitude and longitude
                        formData.latitude = latitude;
                        formData.longitude = longitude;

                        fetch("{{ route('admin.auth.login.submit') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-Requested-With": "XMLHttpRequest", // Recognized as an AJAX request
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}" // CSRF token for security
                                },
                                body: JSON.stringify(formData) // Convert the object to JSON
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status) {
                                    if (data.message.toLowerCase().includes("otp")) {
                                        Swal.fire({
                                            title: "OTP Required",
                                            text: data.message ||
                                                "Please enter the OTP sent to your email.",
                                            icon: "info"
                                        });

                                        // Show the OTP modal
                                        handleOtpModal("twoFactorAuthenticationModal", "open");

                                    } else {
                                        Swal.fire({
                                            title: "Success!",
                                            text: data.message ||
                                                "Login successful! Redirecting...",
                                            icon: "success",
                                            timer: 2000,
                                            showConfirmButton: false
                                        });

                                        setTimeout(() => {
                                            window.location.href = data.redirect_url ||
                                                "/admin"; // Redirect if provided
                                        }, 2000);
                                    }

                                } else {
                                    if (data.errors) {
                                        Object.entries(data.errors).forEach(([key, value]) => {
                                            let input = document.querySelector(`[name="${key}"]`);
                                            if (input) {
                                                input.classList.add("is-invalid");

                                                let feedbackElement = input.closest(".form-group")
                                                    ?.querySelector(".invalid-feedback");
                                                if (feedbackElement) {
                                                    feedbackElement.textContent = value[
                                                        0]; // Show first error message
                                                } else {
                                                    input.insertAdjacentHTML("afterend",
                                                        `<div class="invalid-feedback">${value[0]}</div>`
                                                    );
                                                }
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            title: "Error",
                                            text: data.message ||
                                                "Login failed. Please check your credentials.",
                                            icon: "error"
                                        });
                                    }
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire({
                                    title: "Oops!",
                                    text: "Something went wrong. Please try again later.",
                                    icon: "error"
                                });
                            })
                            .finally(() => {
                                // Enable button & hide spinner
                                toggleButton(loginBtn, spinner, false);
                            });
                    },
                    (errorMessage) => {
                        toggleButton(loginBtn, spinner, false);
                        console.error("Error fetching location:", errorMessage);
                        Swal.fire({
                            icon: "error",
                            title: "Location Error",
                            text: errorMessage,
                            confirmButtonText: "OK"
                        });
                    }
                );

            }
        });
    </script>
</body>

</html>
