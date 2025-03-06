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
    <link href="{{ asset('assets/css/sisodia-dropzone.css') }}" rel="stylesheet">

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
                <div class="form-head page-titles d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">Create New Branch</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.branches.index') }}">Branches</a></li>
                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Branch</a></li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Refresh Button -->
                        <button class="btn btn-primary rounded light" onclick="location.reload();">
                            Refresh
                        </button>
                    </div>
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Branch</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.branches.store') }}" method="POST"
                                    class="needs-validation" id="branchForm" novalidate>
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
                                            <label for="date_of_start" class="form-label fw-bold">Date of
                                                Start</label>
                                            <input type="date" id="date_of_start" name="date_of_start"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type" class="form-label fw-bold">Type <span
                                                    class="text-danger">*</span></label>
                                            <select id="type" name="type" class="form-select" required>
                                                <option value="">Select Type</option>
                                                <option value="head_office">
                                                    Head Office
                                                </option>
                                                <option value="regional">
                                                    Regional
                                                </option>
                                                <option value="franchise">
                                                    Franchise
                                                </option>
                                                <option value="sub_branch">
                                                    Sub Branch
                                                </option>
                                            </select>
                                            <div class="invalid-feedback">Type is required.</div>
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
                                        <div class="col-12 mt-4">
                                            <label for="logo" class="form-label fw-bold">Logo</label>
                                            <input type="file" id="logo" name="logo" class="form-control"
                                                accept="image/*">
                                        </div>

                                        <!-- Section: Address Details -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Address Details</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address_line1" class="form-label fw-bold">Address Line 1 <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="address_line1" name="address_line1"
                                                class="form-control" placeholder="Enter address line 1" required
                                                maxlength="100">
                                            <div class="invalid-feedback">Primary address line is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address_line2" class="form-label fw-bold">Address Line
                                                2</label>
                                            <input type="text" id="address_line2" name="address_line2"
                                                class="form-control" placeholder="Enter address line 2"
                                                maxlength="100">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="country" class="form-label fw-bold">Country <span
                                                    class="text-danger">*</span></label>
                                            <select id="country" name="country_id"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Country is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="state" class="form-label fw-bold">State <span
                                                    class="text-danger">*</span></label>
                                            <select id="state" name="state_id"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select State</option>
                                            </select>
                                            <div class="invalid-feedback">State is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="city" class="form-label fw-bold">City <span
                                                    class="text-danger">*</span></label>
                                            <select id="city" name="city_id" class="form-select dropdown-select"
                                                required>
                                                <option value="">Select City</option>
                                            </select>
                                            <div class="invalid-feedback">City is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="postal_code" class="form-label fw-bold">Postal Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="postal_code" name="postal_code"
                                                class="form-control" placeholder="Enter postal code" required
                                                maxlength="10">
                                            <div class="invalid-feedback">Postal code is required.</div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="branch_latitude" class="form-label fw-bold">Latitude</label>
                                            <input type="number" step="0.0000001" id="branch_latitude"
                                                name="branch_latitude" class="form-control"
                                                placeholder="e.g., 12.9716">
                                            <div class="invalid-feedback">Please enter a valid latitude.</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="branch_longitude" class="form-label fw-bold">Longitude</label>
                                            <input type="number" step="0.0000001" id="branch_longitude"
                                                name="branch_longitude" class="form-control"
                                                placeholder="e.g., 77.5946">
                                            <div class="invalid-feedback">Please enter a valid longitude.</div>
                                        </div>

                                        <!-- Section: Operating Hours -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Operating Hours</h5>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Set Operating Hours by Day</label>
                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <div class="row align-items-center g-2 mb-2 operating-hours-row">
                                                    <div class="col-md-2">
                                                        <label class="form-label fw-bold">{{ $day }}</label>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="input-group clockpicker" data-placement="bottom"
                                                            data-align="top" data-autobtn-close="true">
                                                            <input type="text"
                                                                class="form-control operating-hour-start"
                                                                name="operating_hours[{{ strtolower($day) }}][start]"
                                                                placeholder="Start Time" required>
                                                            <span class="input-group-text"><i
                                                                    class="far fa-clock"></i></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="input-group clockpicker" data-placement="bottom"
                                                            data-align="top" data-autobtn-close="true">
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
                                                                value="1" id="closed_{{ strtolower($day) }}">
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
                                                                value="1" id="open_24_{{ strtolower($day) }}">
                                                            <label class="form-check-label"
                                                                for="open_24_{{ strtolower($day) }}">24
                                                                Hours</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Section: Social Media Links -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Social Media Links</h5>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Add Social Media Profiles</label>
                                            <div id="socialContainer">
                                                <div class="row g-2 mb-2 social-entry">
                                                    <div class="col-md-4">
                                                        <select class="form-select dropdown-select"
                                                            name="social_links[0][platform]">
                                                            <option value="">Select Platform</option>
                                                            <option value="facebook"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/facebook.png') }}">
                                                                Facebook</option>
                                                            <option value="linkedin"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/linkedin.png') }}">
                                                                LinkedIn</option>
                                                            <option value="twitter"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/twitter.png') }}">
                                                                Twitter</option>
                                                            <option value="instagram"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/instagram.png') }}">
                                                                Instagram</option>
                                                            <option value="youtube"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/youtube.png') }}">
                                                                YouTube</option>
                                                            <option value="telegram"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/telegram.png') }}">
                                                                Telegram</option>
                                                            <option value="snapchat"
                                                                data-image="{{ asset('assets/images/iconly/social/circle/snapchat.png') }}">
                                                                Snapchat</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="url" class="form-control"
                                                            name="social_links[0][url]"
                                                            placeholder="Enter URL (e.g., https://facebook.com/yourpage)">
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" id="addSocialBtn"
                                                            class="btn p-2 rounded-circle">
                                                            <img src="{{ asset('assets/images/iconly/action/plus.png') }}"
                                                                alt="Add" class="img-fluid"
                                                                style="max-width: 20px;">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section: Tax Configuration -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">TAX Configuration</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="gstin" class="form-label fw-bold">GSTIN</label>
                                            <input type="text" id="gstin" name="gstin" class="form-control"
                                                placeholder="Enter GSTIN">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Tax Details</label>
                                            <div id="taxContainer">
                                                <div class="row g-2 mb-2 tax-entry">
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control tax-title"
                                                            placeholder="Tax Title (e.g., CGST)">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="number" class="form-control tax-percentage"
                                                            placeholder="Percentage (e.g., 9)">
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" id="addTaxBtn"
                                                            class="btn  p-2 rounded-circle">
                                                            <img src="{{ asset('assets/images/iconly/action/plus.png') }}"
                                                                alt="Add" class="img-fluid"
                                                                style="max-width: 20px;">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section: SMTP Configuration -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">SMTP Configuration</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="use_branch_smtp_credentials"
                                                    name="use_branch_smtp_credentials" value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="use_branch_smtp_credentials">Use Branch SMTP
                                                    Credentials</label>
                                            </div>
                                        </div>
                                        <div class="smtpFields d-none">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label for="smtp_host" class="form-label fw-bold">SMTP Host <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="smtp_host" name="smtp_host"
                                                        class="form-control" placeholder="Enter SMTP host">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Host.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_port" class="form-label fw-bold">SMTP Port <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="smtp_port" name="smtp_port"
                                                        class="form-control" placeholder="Enter SMTP port">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Port.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_username" class="form-label fw-bold">SMTP
                                                        Username <span class="text-danger">*</span></label>
                                                    <input type="text" id="smtp_username" name="smtp_username"
                                                        class="form-control" placeholder="Enter SMTP username">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Username.
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_password" class="form-label fw-bold">SMTP
                                                        Password <span class="text-danger">*</span></label>
                                                    <input type="password" id="smtp_password" name="smtp_password"
                                                        class="form-control" placeholder="Enter SMTP password">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Password.
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_encryption" class="form-label fw-bold">SMTP
                                                        Encryption <span class="text-danger">*</span></label>
                                                    <select id="smtp_encryption" name="smtp_encryption"
                                                        class="form-select dropdown-select">
                                                        <option value="">Select Encryption</option>
                                                        <option value="tls">TLS</option>
                                                        <option value="ssl">SSL</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a valid SMTP Encryption
                                                        type.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_from_email" class="form-label fw-bold">Sender
                                                        Email <span class="text-danger">*</span></label>
                                                    <input type="email" id="smtp_from_email" name="smtp_from_email"
                                                        class="form-control" placeholder="Enter sender email">
                                                    <div class="invalid-feedback">Please enter a valid Sender Email.
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_from_name" class="form-label fw-bold">Sender Name
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" id="smtp_from_name" name="smtp_from_name"
                                                        class="form-control" placeholder="Enter sender name">
                                                    <div class="invalid-feedback">Please enter a valid Sender Name.
                                                    </div>
                                                </div>
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
    <script src="{{ asset('assets/js/sisodia-dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/custom-2.js') }}"></script>

    <script>
        document.getElementById("use_branch_smtp_credentials").addEventListener("change", function() {
            const smtpFields = document.querySelectorAll(
                '.smtpFields'); // Get all elements with the class 'smtpFields'

            if (this.checked) {
                smtpFields.forEach(function(field) {
                    field.classList.remove('d-none');

                    // Add the 'required' attribute to all inputs and selects inside the field
                    field.querySelectorAll('input, select').forEach(function(input) {
                        if (!input.hasAttribute('required')) {
                            input.setAttribute('required', true);
                        }
                    });
                });
            } else {
                console.log("Disabled"); // Log when the checkbox is unchecked
                smtpFields.forEach(function(field) {
                    field.classList.add('d-none');

                    // Remove the 'required' attribute from all inputs and selects inside the field
                    field.querySelectorAll('input, select').forEach(function(input) {
                        input.removeAttribute('required');
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

        $(document).ready(function() {
            function refreshSelect2(selector, options = {}) {
                $(selector).select2({
                    placeholder: options.placeholder || "Select an option",
                    allowClear: options.allowClear !== undefined ? options.allowClear : true
                });
            }

            function populateDropdown(selector, data, placeholder) {
                let dropdown = $(selector);
                dropdown.empty().append('<option value="">' + placeholder + '</option>');
                $.each(data, function(key, value) {
                    dropdown.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                refreshSelect2(selector, {
                    placeholder: placeholder,
                    allowClear: true
                });
            }

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

            $('input[type="file"]').sisodiaDropZone();

            $('#country').on('change', function() {
                let countryId = $(this).val();
                $('#state').html('<option value="">Loading...</option>');
                $('#city').html('<option value="">Select a city</option>');

                if (countryId) {
                    $.ajax({
                        url: `{{ route('states.index', '') }}/${countryId}`,
                        type: "GET",
                        success: function(data) {
                            populateDropdown('#state', data, "Select a state");
                        },
                        error: function() {
                            populateDropdown('#state', [], "Select a state");
                        }
                    });
                } else {
                    populateDropdown('#state', [], "Select a state");
                }
            });

            $('#state').on('change', function() {
                let stateId = $(this).val();
                $('#city').html('<option value="">Loading...</option>');

                if (stateId) {
                    $.ajax({
                        url: `{{ route('cities.index', '') }}/${stateId}`,
                        type: "GET",
                        success: function(data) {
                            populateDropdown('#city', data, "Select a city");
                        },
                        error: function() {
                            populateDropdown('#city', [], "Select a city");
                        }
                    });
                } else {
                    populateDropdown('#city', [], "Select a city");
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("branchForm");
            const taxContainer = document.getElementById("taxContainer");
            const addTaxBtn = document.getElementById("addTaxBtn");
            const socialContainer = document.getElementById("socialContainer");
            const addSocialBtn = document.getElementById("addSocialBtn");
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
            // Add new tax entry
            addTaxBtn.addEventListener("click", function() {
                const taxEntry = document.createElement("div");
                taxEntry.classList.add("row", "g-2", "mb-2", "tax-entry");
                taxEntry.innerHTML = `
                                    <div class="col-md-5">
                                        <input type="text" class="form-control tax-title" placeholder="Tax Title (e.g., CGST)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" class="form-control tax-percentage" placeholder="Percentage (e.g., 9)">
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <button type="button" class="btn remove-tax p-2 rounded-circle">
                                            <img src="{{ asset('assets/images/iconly/action/minus.png') }}" alt="Delete" class="img-fluid" style="max-width: 20px;">
                                        </button>
                                    </div>
                                `;
                taxContainer.appendChild(taxEntry);
            });

            // Remove tax entry (Fix: Ensure clicking the button works even if clicking the image)
            taxContainer.addEventListener("click", function(event) {
                const removeBtn = event.target.closest(
                    ".remove-tax"); // Ensures it works when clicking image inside button
                if (removeBtn) {
                    removeBtn.closest(".tax-entry").remove();
                }
            });

            addSocialBtn.addEventListener("click", function() {
                const index = document.querySelectorAll(".social-entry").length;
                const socialEntry = document.createElement("div");
                socialEntry.classList.add("row", "g-2", "mb-2", "social-entry");
                socialEntry.innerHTML = `
                <div class="col-md-4">
                    <select class="form-select dropdown-select" name="social_links[${index}][platform]">
                        <option value="">Select Platform</option>
                        <option value="facebook">Facebook</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="twitter">Twitter</option>
                        <option value="instagram">Instagram</option>
                        <option value="youtube">YouTube</option>
                        <option value="telegram">Telegram</option>
                        <option value="snapchat">Snapchat</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="url" class="form-control" name="social_links[${index}][url]" placeholder="Enter URL (e.g., https://facebook.com/yourpage)">
                </div>
                <div class="col-md-2 text-center">
                    <button type="button" class="btn remove-social p-2 rounded-circle">
                        <img src="{{ asset('assets/images/iconly/action/minus.png') }}" alt="Delete" class="img-fluid" style="max-width: 20px;">
                    </button>
                </div>`;
                socialContainer.appendChild(socialEntry);
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

            socialContainer.addEventListener("click", function(event) {
                const removeBtn = event.target.closest(".remove-social");
                if (removeBtn) {
                    removeBtn.closest(".social-entry").remove();
                }
            });

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent normal form submission

                let isTaxValid = true;
                let taxData = [];

                let isSocialValid = true;
                let socialData = [];

                // Validate Tax Inputs
                document.querySelectorAll(".tax-entry").forEach(entry => {
                    const titleInput = entry.querySelector(".tax-title");
                    const percentageInput = entry.querySelector(".tax-percentage");

                    const title = titleInput.value.trim();
                    const percentage = percentageInput.value.trim();

                    // Clear previous errors
                    titleInput.classList.remove("is-invalid");
                    percentageInput.classList.remove("is-invalid");

                    let titleFeedback = titleInput.nextElementSibling;
                    let percentageFeedback = percentageInput.nextElementSibling;

                    if (!titleFeedback || !titleFeedback.classList.contains("invalid-feedback")) {
                        titleFeedback = document.createElement("div");
                        titleFeedback.className = "invalid-feedback";
                        titleInput.after(titleFeedback);
                    }

                    if (!percentageFeedback || !percentageFeedback.classList.contains(
                            "invalid-feedback")) {
                        percentageFeedback = document.createElement("div");
                        percentageFeedback.className = "invalid-feedback";
                        percentageInput.after(percentageFeedback);
                    }

                    // Validation logic
                    if ((title && !percentage) || (!title && percentage)) {
                        isTaxValid = false;
                        if (!title) {
                            titleInput.classList.add("is-invalid");
                            titleFeedback.textContent =
                                "Tax Title is required if Percentage is filled.";
                        }
                        if (!percentage) {
                            percentageInput.classList.add("is-invalid");
                            percentageFeedback.textContent =
                                "Percentage is required if Tax Title is filled.";
                        }
                    } else if (title && percentage) {
                        const percentagePattern =
                            /^(0|[1-9]\d*)(\.\d{1,2})?$/; // Allows numbers with up to 2 decimal places

                        if (!percentagePattern.test(percentage)) {
                            isTaxValid = false;
                            percentageInput.classList.add("is-invalid");
                            percentageFeedback.textContent =
                                "Percentage must be a valid number with up to two decimal places (e.g., 1, 5.25, 10.99, 123.00).";
                        } else {
                            taxData.push({
                                title,
                                percentage
                            });
                        }
                    }
                });

                document.querySelectorAll(".social-entry").forEach(entry => {
                    const platformInput = entry.querySelector(".dropdown-select");
                    const urlInput = entry.querySelector(".form-control");

                    const platform = platformInput.value.trim();
                    const url = urlInput.value.trim();

                    // Clear previous errors
                    platformInput.classList.remove("is-invalid");
                    urlInput.classList.remove("is-invalid");

                    // Ensure platformFeedback is placed correctly
                    let platformFeedback = platformInput.parentNode.querySelector(
                        ".platform-feedback");
                    if (!platformFeedback) {
                        platformFeedback = document.createElement("div");
                        platformFeedback.className = "invalid-feedback platform-feedback";
                        platformInput.parentNode.appendChild(
                            platformFeedback); // Append inside parent container
                    }

                    // Ensure urlFeedback is placed correctly
                    let urlFeedback = urlInput.nextElementSibling;
                    if (!urlFeedback || !urlFeedback.classList.contains("invalid-feedback")) {
                        urlFeedback = document.createElement("div");
                        urlFeedback.className = "invalid-feedback";
                        urlInput.after(urlFeedback);
                    }

                    // Validation logic
                    if ((platform && !url) || (!platform && url)) {
                        isSocialValid = false;
                        if (!platform) {
                            platformInput.classList.add("is-invalid");
                            platformFeedback.textContent = "Platform is required if URL is filled.";
                        }
                        if (!url) {
                            urlInput.classList.add("is-invalid");
                            urlFeedback.textContent =
                                "Profile URL is required if Platform is selected.";
                        }
                    } else if (platform && url) {
                        socialData.push({
                            platform,
                            url
                        });
                    }
                });

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

                if (!isTaxValid || !isSocialValid) {
                    // Scroll to first invalid input if any
                    const firstInvalidField = document.querySelector(".is-invalid");

                    if (firstInvalidField) {
                        firstInvalidField.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                        firstInvalidField.focus();
                    }
                    toggleSubmitBtn(false);
                    return;
                }

                // Get the 'use_branch_smtp_credentials' checkbox value
                const useBranchCredentials = document.getElementById("use_branch_smtp_credentials")
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
                        formData.append("use_branch_smtp_credentials", useBranchCredentials);
                        formData.append("tax_details", JSON.stringify(taxData));
                        formData.append("social_links", JSON.stringify(socialData));
                        formData.append("latitude", latitude);
                        formData.append("longitude", longitude);

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
                        formData.append("operating_hours", JSON.stringify(operatingHours));

                        fetch("{{ route('admin.branches.store') }}", {
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
