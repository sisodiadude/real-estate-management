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
    <link href="{{ asset('assets/vendor/clockpicker/css/bootstrap-clockpicker.min.css') }}" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link href="{{ asset('assets/vendor/dropzone/dist/dropzone.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>

    <!-- Preloader Start -->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!-- Preloader End -->

    <!-- Main Wrapper Start -->
    <div id="main-wrapper">

        <!-- Navigation Header Start -->
        @include('admin.layouts.partials.nav-header')
        <!-- Navigation Header End -->

        <!-- Chat Box Section Start -->
        @include('admin.layouts.partials.chat-box')
        <!-- Chat Box Section End -->

        <!-- Header Section Start -->
        @include('admin.layouts.partials.header')
        <!-- Header Section End -->

        <!-- Sidebar Section Start -->
        @include('admin.layouts.partials.sidebar')
        <!-- Sidebar Section End -->

        <!-- Content Body Start -->
        <div class="content-body">
            <div class="container-fluid">
                <div class="page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.branches.index') }}">Branches</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.branches.show', ['branchSlug' => $branch->slug]) }}">
                                {{ $branch->name }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="javascript:void(0)">Add Department</a>
                        </li>
                    </ol>
                </div>

                <!-- row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Department</h4>
                            </div>
                            <div class="card-body">
                                <form
                                    action="{{ route('admin.branches.departments.store', ['branchSlug' => $branch->slug]) }}"
                                    method="POST" class="needs-validation" id="departmentForm" novalidate>
                                    @csrf
                                    <div class="row g-3">
                                        <!-- Section: Basic Details -->
                                        <div class="col-12">
                                            <h5 class="text-primary fw-bold mb-3">Basic Details</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="name" class="form-label fw-bold">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Enter name" required>
                                            <div class="invalid-feedback">Name is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label fw-bold">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Enter email" required>
                                            <div class="invalid-feedback">Valid email is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label fw-bold">Mobile <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="mobile" name="mobile" class="form-control"
                                                placeholder="Enter mobile" required>
                                            <div class="invalid-feedback">Mobile number is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status" class="form-label fw-bold">Status <span
                                                    class="text-danger">*</span></label>
                                            <select id="status" name="status" class="form-select" required>
                                                <option value="">Select Status</option>
                                                <option value="active" selected>Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="suspended">Suspended</option>
                                                <option value="archived">Archived</option>
                                            </select>
                                            <div class="invalid-feedback">Status is required.</div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="description" class="form-label fw-bold">Description</label>
                                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                                        </div>

                                        <!-- Section: Operating Hours -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Operating Hours</h5>
                                        </div>

                                        <!-- "Same as Branch" Checkbox -->
                                        <div class="col-12">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="use_branch_operating_hours" name="use_branch_operating_hours"
                                                    value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="use_branch_operating_hours">Same as Branch</label>
                                            </div>
                                        </div>


                                        <div class="operatingHoursContainer">

                                            <!-- 24/7 Open Checkbox -->
                                            <div class="col-md-12">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="open_24_7"
                                                        name="operating_hours[all][open_24_7]" value="1">
                                                    <label class="form-check-label fw-bold" for="open_24_7">Open
                                                        24/7
                                                        (Applies to All Days)</label>
                                                </div>
                                            </div>
                                            <div class="operating-hours-container">
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Set Operating Hours by
                                                        Day</label>
                                                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                        <div
                                                            class="row align-items-center g-2 mb-2 operating-hours-row">
                                                            <div class="col-md-2">
                                                                <label
                                                                    class="form-label fw-bold">{{ $day }}</label>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="input-group clockpicker"
                                                                    data-placement="bottom" data-align="top"
                                                                    data-autobtn-close="true">
                                                                    <input type="text"
                                                                        class="form-control operating-hour-start"
                                                                        name="operating_hours[{{ strtolower($day) }}][start]"
                                                                        placeholder="Start Time" required>
                                                                    <span class="input-group-text"><i
                                                                            class="far fa-clock"></i></span>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <div class="input-group clockpicker"
                                                                    data-placement="bottom" data-align="top"
                                                                    data-autobtn-close="true">
                                                                    <input type="text"
                                                                        class="form-control operating-hour-end"
                                                                        name="operating_hours[{{ strtolower($day) }}][end]"
                                                                        placeholder="End Time" required>
                                                                    <span class="input-group-text"><i
                                                                            class="far fa-clock"></i></span>
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="col-md-2 d-flex align-items-center justify-content-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input closed-checkbox"
                                                                        type="checkbox"
                                                                        name="operating_hours[{{ strtolower($day) }}][closed]"
                                                                        value="1"
                                                                        id="closed_{{ strtolower($day) }}">
                                                                    <label class="form-check-label"
                                                                        for="closed_{{ strtolower($day) }}">Closed</label>
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="col-md-2 d-flex align-items-center justify-content-center">
                                                                <div class="form-check">
                                                                    <input class="form-check-input open-24-checkbox"
                                                                        type="checkbox"
                                                                        name="operating_hours[{{ strtolower($day) }}][open_24]"
                                                                        value="1"
                                                                        id="open_24_{{ strtolower($day) }}">
                                                                    <label class="form-check-label"
                                                                        for="open_24_{{ strtolower($day) }}">24
                                                                        Hours</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Submit Buttons -->
                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                                <span
                                                    class="submitBtnSpinner spinner-border spinner-submit spinner-border-sm me-2 d-none"
                                                    role="status" aria-hidden="true"></span>
                                                <span id="submit-btn-txt">Submit</span>
                                            </button>
                                            <button type="reset"
                                                class="btn btn-outline-secondary ms-2">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Body End -->

        <!-- Footer Start -->
        @include('admin.layouts.partials.footer')
        <!-- Footer End -->

        <!-- Supprt Ticket Button Start -->
        <!-- Supprt Ticket Button End -->


    </div>
    <!-- Main Wrapper End -->

    <!-- Footer Scripts Section Start -->
    <!-- Required vendors -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/clockpicker/js/bootstrap-clockpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/clock-picker-init.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/dropzone/dist/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/custom-2.js') }}"></script>

    <script>
        function getOperatingHours() {
            const operatingHours = {};

            // Check if "Open 24/7 (All Days)" is enabled
            const open24AllDaysCheckbox = document.querySelector(`[name="operating_hours[all][open_24_7]"]`);
            const isOpen24AllDays = open24AllDaysCheckbox?.checked || false;

            if (isOpen24AllDays) {
                // If Open 24/7 is checked, mark all days as open 24/7
                operatingHours["all"] = {
                    open_24_7: true
                };
            } else {
                // Process individual days
                const days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];
                days.forEach(day => {
                    const startInput = document.querySelector(`[name="operating_hours[${day}][start]"]`);
                    const endInput = document.querySelector(`[name="operating_hours[${day}][end]"]`);
                    const closedCheckbox = document.querySelector(`[name="operating_hours[${day}][closed]"]`);
                    const open24Checkbox = document.querySelector(`[name="operating_hours[${day}][open_24]"]`);

                    operatingHours[day] = {
                        start: startInput?.value || null,
                        end: endInput?.value || null,
                        closed: closedCheckbox?.checked || false,
                        open24: open24Checkbox?.checked || false
                    };
                });
            }

            return operatingHours;
        }
        $(document).ready(function() {
            $('select.dropdown-select').select2({
                width: '100%',
                templateResult: function(option) {
                    if (!option.id) return option.text; // Handle placeholder
                    let imgUrl = $(option.element).data('image');
                    return imgUrl ? $(
                        `<span><img src="${imgUrl}" width="20" class="me-2"/> ${option.text}</span>`
                    ) : $(`<span>${option.text}</span>`);
                },
                templateSelection: function(option) {
                    if (!option.id) return option.text; // Handle placeholder
                    let imgUrl = $(option.element).data('image');
                    return imgUrl ? $(
                        `<span><img src="${imgUrl}" width="20" class="me-2"/> ${option.text}</span>`
                    ) : option.text;
                }
            });
        });

        document.getElementById("use_branch_operating_hours").addEventListener("change", function() {
            const operatingHoursContainer = document.querySelector('.operatingHoursContainer');
            if (this.checked) {
                operatingHoursContainer.classList.add('d-none');

                operatingHoursContainer.querySelectorAll('input, select').forEach(input => {
                    input.removeAttribute('required');
                    input.setAttribute('disabled', true);
                    input.classList.add('opacity-50', 'pe-none'); // Apply Bootstrap styles
                });
            } else {
                operatingHoursContainer.classList.remove('d-none');

                operatingHoursContainer.querySelectorAll('input, select').forEach(input => {
                    input.removeAttribute('disabled');
                    input.classList.remove('opacity-50', 'pe-none'); // Remove Bootstrap styles
                    if (input.classList.contains('operating-hour-start') || input.classList.contains(
                            'operating-hour-end')) {
                        input.setAttribute('required', true);
                    }
                });
            }
        });

        document.getElementById("open_24_7").addEventListener("change", function() {
            const operatingHoursContainer = document.querySelectorAll('.operating-hours-container');

            if (this.checked) {
                operatingHoursContainer.forEach(function(field) {
                    field.classList.add('d-none');

                    // Remove 'required' attribute from all inputs except checkboxes for "Closed" and "24 Hours"
                    field.querySelectorAll('input:not(.closed-checkbox):not(.open-24-checkbox), select')
                        .forEach(function(input) {
                            input.removeAttribute('required');
                        });
                });
            } else {
                operatingHoursContainer.forEach(function(field) {
                    field.classList.remove('d-none');

                    // Add 'required' attribute back to inputs except checkboxes for "Closed" and "24 Hours"
                    field.querySelectorAll('input:not(.closed-checkbox):not(.open-24-checkbox), select')
                        .forEach(function(input) {
                            if (!input.hasAttribute('required')) {
                                input.setAttribute('required', true);
                            }
                        });
                });
            }
        });

        // Handle 24 Hours and Closed Checkbox
        document.querySelectorAll('.open-24-checkbox, .closed-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {

                const row = this.closest('.operating-hours-row');

                const startInput = row.querySelector('.operating-hour-start');
                const endInput = row.querySelector('.operating-hour-end');
                const oppositeCheckbox = row.querySelector(
                    this.classList.contains('open-24-checkbox') ? '.closed-checkbox' :
                    '.open-24-checkbox'
                );

                if (this.checked) {
                    startInput.setAttribute('disabled', true);
                    endInput.setAttribute('disabled', true);
                    oppositeCheckbox.setAttribute('disabled', true);

                    startInput.removeAttribute('required');
                    endInput.removeAttribute('required');

                    // Apply Bootstrap disabled styles
                    startInput.classList.add('opacity-50', 'pe-none');
                    endInput.classList.add('opacity-50', 'pe-none');
                } else {
                    startInput.removeAttribute('disabled');
                    endInput.removeAttribute('disabled');
                    oppositeCheckbox.removeAttribute('disabled');

                    startInput.setAttribute('required', true);
                    endInput.setAttribute('required', true);

                    // Remove Bootstrap disabled styles
                    startInput.classList.remove('opacity-50', 'pe-none');
                    endInput.classList.remove('opacity-50', 'pe-none');
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("departmentForm");
            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent normal form submission

                const createErrorElement = (field, message) => {
                    const errorDiv = document.createElement("div");
                    errorDiv.className = "invalid-feedback";
                    errorDiv.textContent = message;
                    field.parentElement.appendChild(errorDiv);
                    return errorDiv;
                };

                const showError = (field, errorElement, message) => {
                    field.classList.add("is-invalid");
                    errorElement.textContent = message;
                };

                // Validate Mobile Number
                const validateMobile = () => {
                    const mobileInput = document.getElementById("mobile");

                    const mobilePattern = /^\+?[0-9\s-]{10,20}$/; // ✅ Fixed regex

                    const mobileError = mobileInput.parentElement.querySelector(".invalid-feedback") ||
                        createErrorElement(mobileInput, "Invalid mobile number.");

                    // Remove previous invalid class
                    mobileInput.classList.remove("is-invalid");

                    const mobileValue = mobileInput.value.trim();

                    if (!mobilePattern.test(mobileValue)) {
                        showError(mobileInput, mobileError, "Please enter a valid mobile number.");
                        return false; // Validation failed
                    } else {
                        return true; // Validation passed
                    }
                };

                function formChecker(form) {
                    // Run mobile validation before form checking
                    const isMobileValid = validateMobile();

                    // Add Bootstrap validation styling
                    form.classList.add("was-validated");

                    // Identify and highlight invalid inputs
                    const invalidInputs = form.querySelectorAll(".is-invalid");

                    if (invalidInputs.length === 0 && isMobileValid) {
                        form.classList.remove("was-validated"); // Ensure clean state
                        return true;
                    }

                    // Focus on the first invalid field
                    const firstInvalidField = invalidInputs[0];
                    firstInvalidField.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                    firstInvalidField.focus();

                    return false;
                }

                if (!formChecker(form)) {
                    return;
                }

                // Get the 'use_branch_operating_hours' checkbox value
                const useBranchOperatingHours = document.getElementById("use_branch_operating_hours")
                    .checked ? 1 :
                    0;

                // AJAX Submission
                toggleButton("#submitBtn", {
                    textSelector: "#submit-btn-txt",
                    oldText: "Submit",
                    newText: "Submitting...",
                    spinnerSelector: ".submitBtnSpinner"
                }, true);

                getCurrentLocation(
                    (location) => {
                        console.log("Successfully fetched location:", location);

                        const {
                            latitude,
                            longitude
                        } = location;

                        const formData = new FormData(form);
                        formData.set("use_branch_operating_hours", useBranchOperatingHours);
                        formData.set("latitude", latitude);
                        formData.set("longitude", longitude);
                        formData.set("operating_hours", JSON.stringify(getOperatingHours()));

                        fetch("{{ route('admin.branches.departments.store', ['branchSlug' => $branch->slug]) }}", {
                                method: "POST",
                                body: formData,
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Accept": "application/json"
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                const messageBox = document.getElementById("responseMessage");

                                if (data.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: data
                                            .message, // Optional message that comes from the response
                                        confirmButtonText: 'OK',
                                        timer: 3000, // Automatically close after 3 seconds
                                        timerProgressBar: true, // Optional: to show a progress bar while the timer counts down
                                    }).then(() => {
                                        // Redirect to the URL passed in the response after the Swal is closed
                                        window.location.href = data.redirect_url;
                                    });
                                } else {
                                    if (data.errors) {
                                        let firstInput = null;
                                        Object.entries(data.errors).forEach(([key, value]) => {
                                            const input = document.querySelector(
                                                `[name="${key}"]`);
                                            if (input) {
                                                firstInput = firstInput || input;
                                                input.classList.add("is-invalid");
                                                (input.closest(".form-group")
                                                    ?.querySelector(".invalid-feedback") ||
                                                    createErrorElement(input, ""))
                                                .textContent = value[0];
                                            }
                                        });

                                        // Ensure firstInput exists before calling focus
                                        if (firstInput) {
                                            firstInput.scrollIntoView({
                                                behavior: "smooth",
                                                block: "center"
                                            });
                                            firstInput.focus();
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: data.message || "Something went wrong!"
                                        });
                                    }
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                            })
                            .finally(() => {
                                toggleButton("#submitBtn", {
                                    textSelector: "#submit-btn-txt",
                                    oldText: "Submit",
                                    newText: "Submitting...",
                                    spinnerSelector: ".submitBtnSpinner"
                                }, false);
                            });
                    },
                    (errorMessage) => {
                        toggleButton("#submitBtn", {
                            textSelector: "#submit-btn-txt",
                            oldText: "Submit",
                            newText: "Submitting...",
                            spinnerSelector: ".submitBtnSpinner"
                        }, false);
                        console.error("Error fetching location:", errorMessage);
                        Swal.fire({
                            icon: "error",
                            title: "Location Error",
                            text: errorMessage,
                            confirmButtonText: "OK"
                        });
                    }
                );
            });
        });
    </script>
</body>

</html>
