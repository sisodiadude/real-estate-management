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
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0)">
                                {{ $branch->slug }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="javascript:void(0)">Edit</a>
                        </li>
                    </ol>
                </div>

                <!-- row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Department</h4>
                            </div>
                            <div class="card-body">
                                <form
                                    action="{{ route('admin.branches.departments.edit', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug]) }}"
                                    method="POST" class="needs-validation" id="branchForm" novalidate>
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <!-- Section: Basic Details -->
                                        <div class="col-12">
                                            <h5 class="text-primary fw-bold mb-3">Basic Details</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="name" class="form-label fw-bold">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Enter name" value="{{ old('name', $department->name) }}"
                                                required>
                                            <div class="invalid-feedback">Name is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label fw-bold">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Enter email"
                                                value="{{ old('email', $department->email) }}" required>
                                            <div class="invalid-feedback">Valid email is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label fw-bold">Mobile <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="mobile" name="mobile" class="form-control"
                                                placeholder="Enter mobile"
                                                value="{{ old('mobile', $department->mobile) }}" required>
                                            <div class="invalid-feedback">Mobile number is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status" class="form-label fw-bold">Status <span
                                                    class="text-danger">*</span></label>
                                            <select id="status" name="status" class="form-select" required>
                                                <option value="">Select Status</option>
                                                <option value="active"
                                                    {{ old('status', $department->status) == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', $department->status) == 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                                <option value="suspended"
                                                    {{ old('status', $department->status) == 'suspended' ? 'selected' : '' }}>
                                                    Suspended</option>
                                                <option value="archived"
                                                    {{ old('status', $department->status) == 'archived' ? 'selected' : '' }}>
                                                    Archived</option>
                                            </select>
                                            <div class="invalid-feedback">Status is required.</div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="description" class="form-label fw-bold">Description</label>
                                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter description">{{ old('description', $department->description) }}</textarea>
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
                                                    {{ $department->use_branch_operating_hours ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold"
                                                    for="use_branch_operating_hours">Same as Branch</label>
                                            </div>
                                        </div>

                                        <div class="operatingHoursContainer">
                                            <div class="col-12">
                                                @php
                                                    $operatingHours =
                                                        json_decode($department->operating_hours, true) ?? [];
                                                @endphp
                                                <label class="form-label fw-bold">Set Operating Hours by Day</label>
                                                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                    @php
                                                        $dayKey = strtolower($day);
                                                        $start = $operatingHours[$dayKey]['start'] ?? '';
                                                        $end = $operatingHours[$dayKey]['end'] ?? '';
                                                        $closed =
                                                            isset($operatingHours[$dayKey]['closed']) &&
                                                            $operatingHours[$dayKey]['closed']
                                                                ? 'checked'
                                                                : '';
                                                        $allTimeOpen =
                                                            isset($operatingHours[$dayKey]['allTimeOpen']) &&
                                                            $operatingHours[$dayKey]['allTimeOpen']
                                                                ? 'checked'
                                                                : '';
                                                    @endphp

                                                    <div class="row align-items-center g-2 mb-2 operating-hours-row">
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
                                                                    value="{{ $start }}"
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
                                                                    value="{{ $end }}"
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
                                                                    value="1" id="closed_{{ strtolower($day) }}"
                                                                    {{ $closed }}>
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
                                                                    id="open_24_{{ strtolower($day) }}"
                                                                    {{ $allTimeOpen }}>
                                                                <label class="form-check-label"
                                                                    for="open_24_{{ strtolower($day) }}">24
                                                                    Hours</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <span
                                                    class="spinner-border spinner-submit spinner-border-sm me-2 d-none"
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
            const form = document.getElementById("branchForm");
            const submitBtn = form.querySelector('button[type="submit"]');
            const spinner = submitBtn.querySelector('.spinner-border');
            const submitText = submitBtn.querySelector('#submit-btn-txt'); // Get the text inside the submit button

            function toggleSubmitBtn(isDisabled) {
                if (!submitBtn) return;

                submitBtn.disabled = isDisabled;
                submitText.textContent = isDisabled ? "Submitting..." : "Submit";

                // Toggle spinner visibility based on the state
                if (isDisabled) {
                    // form.style.filter = "blur(5px)";
                    spinner.classList.remove("d-none");
                } else {
                    // form.style.filter = "none";
                    spinner.classList.add("d-none");
                }

                if (!isDisabled) return;

                // Clear previous validation errors
                document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
                document.querySelectorAll(".invalid-feedback").forEach(el => el.textContent = "");
            }

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent normal form submission

                if (!form.checkValidity()) {
                    form.classList.add("was-validated");

                    // Find the first invalid input field
                    const firstInvalidInput = form.querySelector(":invalid");

                    if (firstInvalidInput) {
                        firstInvalidInput.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        }); // Scroll to the field
                        firstInvalidInput.focus(); // Set focus
                    }

                    return;
                }

                // Get the 'use_branch_operating_hours' checkbox value
                const useBranchOperatingHours = document.getElementById("use_branch_operating_hours")
                    .checked ? 1 :
                    0;

                // AJAX Submission
                toggleSubmitBtn(true);

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

                        const operatingHours = {};

                        // Loop through each day to get its values
                        ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]
                        .forEach(
                            day => {
                                const startInput = document.querySelector(
                                    `[name="operating_hours[${day}][start]"]`);
                                const endInput = document.querySelector(
                                    `[name="operating_hours[${day}][end]"]`);
                                const closedCheckbox = document.querySelector(
                                    `[name="operating_hours[${day}][closed]"]`);
                                const allTimeOpenCheckbox = document.querySelector(
                                    `[name="operating_hours[${day}][open_24]"]`);

                                operatingHours[day] = {
                                    start: startInput ? startInput.value : null,
                                    end: endInput ? endInput.value : null,
                                    closed: closedCheckbox ? closedCheckbox.checked : false,
                                    allTimeOpen: allTimeOpenCheckbox ? allTimeOpenCheckbox
                                        .checked : false
                                };
                            });

                        // Append operating hours JSON to formData
                        formData.set("operating_hours", JSON.stringify(operatingHours));

                        fetch("{{ route('admin.branches.departments.edit', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug]) }}", {
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
                                        let firstInput = null; // Initialize firstInput

                                        Object.entries(data.errors).forEach(([key, value],
                                            index) => {
                                            const input = document.querySelector(
                                                `[name="${key}"]`);
                                            if (input) {
                                                if (!
                                                    firstInput
                                                ) { // Set firstInput only once
                                                    firstInput = input;
                                                }

                                                input.classList.add("is-invalid");
                                                const feedbackElement = input.closest(
                                                        ".form-group")
                                                    ?.querySelector(".invalid-feedback");
                                                if (feedbackElement) {
                                                    feedbackElement.textContent = value[0];
                                                } else {
                                                    input.insertAdjacentHTML("afterend",
                                                        `<div class="invalid-feedback">${value[0]}</div>`
                                                    );
                                                }
                                            }
                                        });

                                        if (firstInput) {
                                            firstInput.scrollIntoView({
                                                behavior: "smooth",
                                                block: "center"
                                            }); // Scroll to the first invalid field
                                            firstInput
                                                .focus(); // Set focus on the first invalid input
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
                                document.getElementById("responseMessage").innerHTML =
                                    `<div class="alert alert-danger">An unexpected error occurred.</div>`;
                            })
                            .finally(() => {
                                toggleSubmitBtn(false);
                            });
                    },
                    (errorMessage) => {
                        toggleSubmitBtn(false);
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
