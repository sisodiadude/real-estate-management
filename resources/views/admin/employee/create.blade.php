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
    <!-- Vendor Styles -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <!-- Pickadate Styles -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/pickadate/themes/default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/pickadate/themes/default.date.css') }}">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/sisodia-dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
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
                        <h4 class="mb-1 fw-bold">Create New Employee</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.branches.index') }}">Branches</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.branches.show', ['branchSlug' => $branch->slug]) }}">{{ $branch->name }}</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.branches.departments.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug]) }}">{{ $department->name }}</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.branches.departments.teams.show', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug]) }}">{{ $team->name }}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Employee</a></li>
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
                                <h4 class="card-title">Add Employee</h4>
                            </div>
                            <div class="card-body">
                                <form
                                    action="{{ route('admin.branches.departments.teams.employees.store', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug]) }}"
                                    method="POST" class="needs-validation" id="branchForm" novalidate
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <!-- Section: Personal Information -->
                                        <div class="col-12">
                                            <h5 class="text-primary fw-bold mb-3">Personal Information</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="first_name" class="form-label fw-bold">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="first_name" name="first_name" class="form-control"
                                                placeholder="Enter first name" required>
                                            <div class="invalid-feedback">First name is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="last_name" class="form-label fw-bold">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="last_name" name="last_name"
                                                class="form-control" placeholder="Enter last name" required>
                                            <div class="invalid-feedback">Last name is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label fw-bold">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Enter email" required>
                                            <div class="invalid-feedback">Valid email is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="alternative_email" class="form-label fw-bold">Alternative
                                                Email</label>
                                            <input type="email" id="alternative_email" name="alternative_email"
                                                class="form-control" placeholder="Enter alternative email">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label fw-bold">Mobile <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="mobile" name="mobile" class="form-control"
                                                placeholder="Enter mobile number" required>
                                            <div class="invalid-feedback">Valid mobile number is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="alternate_mobile" class="form-label fw-bold">Alternate
                                                Mobile</label>
                                            <input type="text" id="alternate_mobile" name="alternate_mobile"
                                                class="form-control" placeholder="Enter alternate mobile number">
                                            <div class="invalid-feedback">Valid alternate mobile number is required.
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="date_of_birth" class="form-label fw-bold">Date of Birth <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="date_of_birth" name="date_of_birth"
                                                class="form-control" required max="{{ date('Y-m-d') }}">
                                            <div class="invalid-feedback">Date of birth is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="marital_status" class="form-label fw-bold">Marital
                                                Status</label>
                                            <select id="marital_status" name="marital_status"
                                                class="form-select dropdown-select">
                                                <option value="">Select</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                                <option value="divorced">Divorced</option>
                                                <option value="widowed">Widowed</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="nationality" class="form-label fw-bold">Nationality <span
                                                    class="text-danger">*</span></label>
                                            <select id="nationality" name="nationality_id"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select Nationality</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->nationality }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Country is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="blood_group" class="form-label fw-bold">Blood Group</label>
                                            <select id="blood_group" name="blood_group"
                                                class="form-select dropdown-select">
                                                <option value="">Select</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="account_status" class="form-label fw-bold">Status <span
                                                    class="text-danger">*</span></label>
                                            <select id="account_status" name="account_status"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select status</option>
                                                <option value="active" selected>Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="suspended">Suspended</option>
                                                <option value="archived">Archived</option>
                                            </select>
                                            <div class="invalid-feedback">Status is required.</div>
                                        </div>

                                        <!-- Section: Current Address -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Current Address</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="current_address_line1" class="form-label fw-bold">Address Line
                                                1 <span class="text-danger">*</span></label>
                                            <input type="text" id="current_address_line1"
                                                name="current_address_line1" class="form-control"
                                                placeholder="Enter address line 1" required maxlength="100">
                                            <div class="invalid-feedback">Address Line 1 is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="current_address_line2" class="form-label fw-bold">Address Line
                                                2</label>
                                            <input type="text" id="current_address_line2"
                                                name="current_address_line2" class="form-control"
                                                placeholder="Enter address line 2" maxlength="100">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="current_country" class="form-label fw-bold">Country <span
                                                    class="text-danger">*</span></label>
                                            <select id="current_country" name="current_country_id"
                                                class="form-select dropdown-select"
                                                data-state-route="{{ route('states.index', '') }}" required>
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Country is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="current_state" class="form-label fw-bold">State <span
                                                    class="text-danger">*</span></label>
                                            <select id="current_state" name="current_state_id"
                                                class="form-select dropdown-select"
                                                data-city-route="{{ route('cities.index', '') }}" required>
                                                <option value="">Select State</option>
                                            </select>
                                            <div class="invalid-feedback">State is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="current_city" class="form-label fw-bold">City <span
                                                    class="text-danger">*</span></label>
                                            <select id="current_city" name="current_city_id"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select City</option>
                                            </select>
                                            <div class="invalid-feedback">City is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="current_postal_code" class="form-label fw-bold">Postal Code
                                                <span class="text-danger">*</span></label>
                                            <input type="text" id="current_postal_code" name="current_postal_code"
                                                class="form-control" placeholder="Enter postal code" required
                                                maxlength="10">
                                            <div class="invalid-feedback">Postal code is required.</div>
                                        </div>

                                        <!-- Checkbox for Same Address -->
                                        <div class="col-12 mt-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="same_as_current_address" name="same_as_current_address"
                                                    value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="same_as_current_address">Same as
                                                    Current Address</label>
                                            </div>
                                        </div>

                                        <!-- Section: Permanent Address -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Permanent Address</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="permanent_address_line1" class="form-label fw-bold">Address
                                                Line 1 <span class="text-danger">*</span></label>
                                            <input type="text" id="permanent_address_line1"
                                                name="permanent_address_line1" class="form-control"
                                                placeholder="Enter address line 1" required maxlength="100">
                                            <div class="invalid-feedback">Address Line 1 is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="permanent_address_line2" class="form-label fw-bold">Address
                                                Line 2</label>
                                            <input type="text" id="permanent_address_line2"
                                                name="permanent_address_line2" class="form-control"
                                                placeholder="Enter address line 2" maxlength="100">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="permanent_country" class="form-label fw-bold">Country <span
                                                    class="text-danger">*</span></label>
                                            <select id="permanent_country" name="permanent_country_id"
                                                class="form-select dropdown-select"
                                                data-state-route="{{ route('states.index', '') }}" required>
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Country is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="permanent_state" class="form-label fw-bold">State <span
                                                    class="text-danger">*</span></label>
                                            <select id="permanent_state" name="permanent_state_id"
                                                class="form-select dropdown-select"
                                                data-city-route="{{ route('cities.index', '') }}" required>
                                                <option value="">Select State</option>
                                            </select>
                                            <div class="invalid-feedback">State is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="permanent_city" class="form-label fw-bold">City <span
                                                    class="text-danger">*</span></label>
                                            <select id="permanent_city" name="permanent_city_id"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select City</option>
                                            </select>
                                            <div class="invalid-feedback">City is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="permanent_postal_code" class="form-label fw-bold">Postal Code
                                                <span class="text-danger">*</span></label>
                                            <input type="text" id="permanent_postal_code"
                                                name="permanent_postal_code" class="form-control"
                                                placeholder="Enter postal code" required maxlength="10">
                                            <div class="invalid-feedback">Postal code is required.</div>
                                        </div>

                                        <!-- Section: Employment Details -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Employment Details</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="designation" class="form-label fw-bold">Designation <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="designation" name="designation"
                                                class="form-control" placeholder="Enter designation" required>
                                            <div class="invalid-feedback">Designation is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="joining_date" class="form-label fw-bold">Joining Date <span
                                                    class="text-danger">*</span></label>
                                            <input name="joining_date" class="form-control pickdate-picker">
                                            <div class="invalid-feedback">Joining date is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="probation_period" class="form-label fw-bold">Probation Period
                                                (Months)</label>
                                            <input type="number" id="probation_period" name="probation_period"
                                                class="form-control" min="0" max="24"
                                                placeholder="Enter months">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="employment_type" class="form-label fw-bold">Employment
                                                Type <span class="text-danger">*</span></label>
                                            <select id="employment_type" name="employment_type"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select</option>
                                                <option value="full_time">Full-Time</option>
                                                <option value="part_time" selected>Part-Time</option>
                                                <option value="contract">Contract</option>
                                                <option value="internship">Internship</option>
                                            </select>
                                        </div>

                                        <!-- Section: Salary & Banking -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Salary & Banking</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="salary" class="form-label fw-bold">Salary <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" id="salary" name="salary" class="form-control"
                                                min="0" step="0.01" placeholder="Enter salary" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bank_account" class="form-label fw-bold">Bank Account
                                                Number <span class="text-danger">*</span></label>
                                            <input type="text" id="bank_account" name="bank_account"
                                                class="form-control" placeholder="Enter account number" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="bank_name" class="form-label fw-bold">Bank Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="bank_name" name="bank_name"
                                                class="form-control" placeholder="Enter bank name" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Allowances</label>
                                            <div id="allowanceContainer">
                                                <div class="row g-2 mb-2 allowance-entry">
                                                    <div class="col-md-6">
                                                        <select class="form-select dropdown-select allowance-type"
                                                            name="allowances[0][type]">
                                                            <option value="" selected>Select Allowance</option>
                                                            <option value="house_rent">House Rent Allowance (HRA)
                                                            </option>
                                                            <option value="dearness">Dearness Allowance (DA)</option>
                                                            <option value="travel">Travel Allowance (TA)</option>
                                                            <option value="medical">Medical Allowance</option>
                                                            <option value="conveyance">Conveyance Allowance</option>
                                                            <option value="performance_bonus">Performance Bonus
                                                            </option>
                                                            <option value="overtime">Overtime Allowance</option>
                                                            <option value="food">Food Allowance</option>
                                                            <option value="education">Education Allowance</option>
                                                            <option value="special">Special Allowance</option>
                                                            <option value="entertainment">Entertainment Allowance
                                                            </option>
                                                            <option value="communication">Communication Allowance
                                                            </option>
                                                            <option value="internet">Internet Allowance</option>
                                                            <option value="shift">Shift Allowance</option>
                                                            <option value="leave_travel">Leave Travel Allowance (LTA)
                                                            </option>
                                                            <option value="uniform">Uniform Allowance</option>
                                                            <option value="child_education">Child Education Allowance
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control allowance-amount"
                                                            name="allowances[0][amount]" placeholder="Enter amount"
                                                            step="0.01" min="0">
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" id="addAllowanceBtn"
                                                            class="btn p-2 rounded-circle">
                                                            <img src="{{ asset('assets/images/iconly/action/plus.png') }}"
                                                                alt="Add" class="img-fluid"
                                                                style="max-width: 20px;">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                Allowances are subject to company policy.
                                                <a href="{{ asset('assets/docs/policies/company-allowance-policy.pdf') }}"
                                                    target="_blank">View terms</a>.
                                            </small>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Deductions</label>
                                            <div id="deductionContainer">
                                                <div class="row g-2 mb-2 deduction-entry">
                                                    <div class="col-md-6">
                                                        <select class="form-select dropdown-select deduction-type"
                                                            name="deductions[0][type]">
                                                            <option value="" selected>Select deduction type
                                                            </option>
                                                            <option value="tax">Tax Deduction</option>
                                                            <option value="insurance">Insurance Deduction</option>
                                                            <option value="retirement">Retirement Fund</option>
                                                            <option value="loan">Loan Repayment</option>
                                                            <option value="other">Other Deductions</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control deduction-amount"
                                                            name="deductions[0][amount]" placeholder="Enter amount"
                                                            step="0.01" min="0">
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <button type="button" id="addDeductionBtn"
                                                            class="btn p-2 rounded-circle">
                                                            <img src="{{ asset('assets/images/iconly/action/plus.png') }}"
                                                                alt="Add" class="img-fluid"
                                                                style="max-width: 20px;">
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="ifsc_swift_code" class="form-label fw-bold">IFSC/SWIFT Code
                                                <span class="text-danger">*</span>
                                                <span data-toggle="tooltip" data-placement="right"
                                                    title="Enter IFSC for India or SWIFT for international banks">
                                                    <i class="las la-info-circle"></i>
                                                </span>
                                            </label>
                                            <input type="text" id="ifsc_swift_code" name="ifsc_swift_code"
                                                class="form-control" placeholder="Enter IFSC or SWIFT code" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pan_tax_id" class="form-label fw-bold">PAN or Tax ID <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="pan_tax_id" name="pan_tax_id"
                                                class="form-control" placeholder="Enter PAN or Tax ID" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="salary_frequency" class="form-label fw-bold">Salary Payment
                                                Frequency <span class="text-danger">*</span></label>
                                            <select name="salary_frequency" id="salary_frequency"
                                                class="form-select dropdown-select"
                                                aria-label="Select Salary Frequency" required>
                                                <option value="" selected>Select Salary Frequency</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="biweekly">Bi-Weekly (Every 2 Weeks)</option>
                                                <option value="semimonthly">Semi-Monthly (1st & 15th)</option>
                                                <option value="monthly" selected>Monthly</option>
                                                <option value="quarterly">Quarterly</option>
                                                <option value="semiannually">Semi-Annually (Every 6 Months)</option>
                                                <option value="annually">Annually (Yearly)</option>
                                            </select>
                                        </div>

                                        <!-- Section: Emergency Contact -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Emergency Contact</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="emergency_contact_name"
                                                class="form-label fw-bold">Name</label>
                                            <input type="text" id="emergency_contact_name"
                                                name="emergency_contact_name" class="form-control"
                                                placeholder="Enter name">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="emergency_contact_relation"
                                                class="form-label fw-bold">Relationship</label>
                                            <input type="text" id="emergency_contact_relation"
                                                name="emergency_contact_relation" class="form-control"
                                                placeholder="Enter relationship">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="emergency_contact_number" class="form-label fw-bold">Contact
                                                Number</label>
                                            <input type="text" id="emergency_contact_number"
                                                name="emergency_contact_number" class="form-control"
                                                placeholder="Enter contact number">
                                        </div>

                                        <!-- Section: Document Uploads -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Document Uploads</h5>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="resume" class="form-label fw-bold">Resume/CV <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" id="resume" name="resume" class="form-control"
                                                accept=".pdf,image/*" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="profile_picture" class="form-label fw-bold">Profile
                                                Picture <span class="text-danger">*</span></label>
                                            <input type="file" id="profile_picture" name="profile_picture"
                                                class="form-control" accept="image/*" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="govt_id" class="form-label fw-bold">Government ID <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" id="govt_id" name="govt_id" class="form-control"
                                                accept=".pdf,image/*" multiple required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="education_certificates" class="form-label fw-bold">Education
                                                Certificates <span class="text-danger">*</span></label>
                                            <input type="file" id="education_certificates"
                                                name="education_certificates" class="form-control"
                                                accept=".pdf,image/*" multiple required>
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
    <!-- Required Vendors -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/pickadate/picker.js') }}"></script>
    <script src="{{ asset('assets/vendor/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('assets/vendor/pickadate/picker.time.js') }}"></script>

    <!-- Plugin Initializations -->
    <script src="{{ asset('assets/js/plugins-init/pickadate-init.js') }}"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/custom-2.js') }}"></script>
    <script src="{{ asset('assets/js/sisodia-dropzone.js') }}"></script>
    <script>
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

        function handleDropdownChange(triggerSelector, targetSelector, routeDataAttr, defaultPlaceholder,
            refreshSelectors = {}) {
            $(document).on('change', triggerSelector, function() {
                let selectedId = $(this).val();
                let routeBase = $(this).data(
                    routeDataAttr); // Retrieve the route from the data attribute

                // Display a loading placeholder
                $(targetSelector).html('<option value="">Loading...</option>');

                if (selectedId && routeBase) {
                    $.ajax({
                        url: `${routeBase}/${selectedId}`,
                        type: "GET",
                        success: function(data) {
                            populateDropdown(targetSelector, data, defaultPlaceholder);
                        },
                        error: function() {
                            populateDropdown(targetSelector, [], defaultPlaceholder);
                        }
                    });
                } else {
                    populateDropdown(targetSelector, [], defaultPlaceholder);
                }

                // Reset dependent dropdowns
                $.each(refreshSelectors, function(selector, placeholder) {
                    populateDropdown(selector, [], placeholder);
                });
            });
        }

        $(document).ready(function() {
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            })

            $('select.dropdown-select').select2();

            $('input[type="file"]').sisodiaDropZone();
            // $('#resume').sisodiaDropZone();

            // Bind event handlers
            handleDropdownChange('#current_country', '#current_state', 'state-route', 'Select a state', {
                "#current_state": "Select a state",
                "#current_city": "Select a city"
            });

            handleDropdownChange('#current_state', '#current_city', 'city-route', 'Select a city', {
                "#current_city": "Select a city"
            });

            handleDropdownChange('#permanent_country', '#permanent_state', 'state-route', 'Select a state', {
                "#permanent_state": "Select a state",
                "#permanent_city": "Select a city"
            });

            handleDropdownChange('#permanent_state', '#permanent_city', 'city-route', 'Select a city', {
                "#permanent_city": "Select a city"
            });

        });

        document.getElementById("same_as_current_address").addEventListener("change", function() {
            const getAddressValue = (id) => document.getElementById(id).value;
            const setFieldValue = (id, value) => document.getElementById(id).value = value;

            const currentAddress = {
                line1: getAddressValue("current_address_line1"),
                line2: getAddressValue("current_address_line2"),
                postalCode: getAddressValue("current_postal_code"),
                country: getAddressValue("current_country"),
                state: getAddressValue("current_state"),
                city: getAddressValue("current_city")
            };

            const permanentFields = {
                line1: "permanent_address_line1",
                line2: "permanent_address_line2",
                postalCode: "permanent_postal_code",
                country: "permanent_country",
                state: "permanent_state",
                city: "permanent_city"
            };

            const updateDropdown = (selector, value, callback = null) => {
                $(selector).val(value).trigger('change');
                if (callback) setTimeout(callback, 1000);
            };

            if (this.checked) {
                setFieldValue(permanentFields.line1, currentAddress.line1);
                setFieldValue(permanentFields.line2, currentAddress.line2);
                setFieldValue(permanentFields.postalCode, currentAddress.postalCode);

                updateDropdown(`#${permanentFields.country}`, currentAddress.country, () => {
                    updateDropdown(`#${permanentFields.state}`, currentAddress.state, () => {
                        updateDropdown(`#${permanentFields.city}`, currentAddress.city);
                    });
                });
            } else {
                Object.values(permanentFields).forEach(field => setFieldValue(field, ""));

                updateDropdown(`#${permanentFields.country}`, "", () => {
                    updateDropdown(`#${permanentFields.state}`, "", () => {
                        updateDropdown(`#${permanentFields.city}`, "");
                    });
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("branchForm");
            const allowanceContainer = document.getElementById("allowanceContainer");
            const addAllowanceBtn = document.getElementById("addAllowanceBtn");
            const deductionContainer = document.getElementById("deductionContainer");
            const addDeductionBtn = document.getElementById("addDeductionBtn");
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

            // Add new allowance entry
            addAllowanceBtn.addEventListener("click", function() {
                const index = document.querySelectorAll(".allowance-entry").length;
                const allowanceEntry = document.createElement("div");
                allowanceEntry.classList.add("row", "g-2", "mb-2", "allowance-entry");
                allowanceEntry.innerHTML = `
                    <div class="col-md-6">
                        <select class="form-select dropdown-select allowance-type" name="allowances[${index}][type]">
                            <option value="" selected>Select Allowance</option>
                            <option value="house_rent">House Rent Allowance (HRA)</option>
                            <option value="dearness">Dearness Allowance (DA)</option>
                            <option value="travel">Travel Allowance (TA)</option>
                            <option value="medical">Medical Allowance</option>
                            <option value="conveyance">Conveyance Allowance</option>
                            <option value="performance_bonus">Performance Bonus</option>
                            <option value="overtime">Overtime Allowance</option>
                            <option value="food">Food Allowance</option>
                            <option value="education">Education Allowance</option>
                            <option value="special">Special Allowance</option>
                            <option value="entertainment">Entertainment Allowance</option>
                            <option value="communication">Communication Allowance</option>
                            <option value="internet">Internet Allowance</option>
                            <option value="shift">Shift Allowance</option>
                            <option value="leave_travel">Leave Travel Allowance (LTA)</option>
                            <option value="uniform">Uniform Allowance</option>
                            <option value="child_education">Child Education Allowance</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" class="form-control allowance-amount" name="allowances[${index}][amount]" placeholder="Enter amount" step="0.01" min="0">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn remove-allowance p-2 rounded-circle">
                            <img src="{{ asset('assets/images/iconly/action/minus.png') }}" alt="Delete" class="img-fluid" style="max-width: 20px;">
                        </button>
                    </div>`;
                allowanceContainer.appendChild(allowanceEntry);
                $('select.dropdown-select').select2();
            });

            // Remove allowance entry (Fix: Ensure clicking the button works even if clicking the image)
            allowanceContainer.addEventListener("click", function(event) {
                const removeBtn = event.target.closest(
                    ".remove-allowance"); // Ensures it works when clicking image inside button
                if (removeBtn) {
                    removeBtn.closest(".allowance-entry").remove();
                }
            });

            // Add new tax entry
            addDeductionBtn.addEventListener("click", function() {
                const index = document.querySelectorAll(".deduction-entry").length;
                const allowanceEntry = document.createElement("div");
                allowanceEntry.classList.add("row", "g-2", "mb-2", "deduction-entry");
                allowanceEntry.innerHTML = `
                    <div class="col-md-6">
                        <select class="form-select dropdown-select deduction-type" name="deductions[${index}][type]">
                            <option value="" selected>Select deduction type</option>
                            <option value="tax">Tax Deduction</option>
                            <option value="insurance">Insurance Deduction</option>
                            <option value="retirement">Retirement Fund</option>
                            <option value="loan">Loan Repayment</option>
                            <option value="other">Other Deductions</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" class="form-control deduction-amount" name="deductions[${index}][amount]" placeholder="Enter amount" step="0.01" min="0">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn remove-deduction p-2 rounded-circle">
                            <img src="{{ asset('assets/images/iconly/action/minus.png') }}" alt="Delete" class="img-fluid" style="max-width: 20px;">
                        </button>
                    </div>`;
                deductionContainer.appendChild(allowanceEntry);
                $('select.dropdown-select').select2();
            });

            // Remove deduction entry (Fix: Ensure clicking the button works even if clicking the image)
            deductionContainer.addEventListener("click", function(event) {
                const removeBtn = event.target.closest(
                    ".remove-deduction"); // Ensures it works when clicking image inside button
                if (removeBtn) {
                    removeBtn.closest(".deduction-entry").remove();
                }
            });

            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent normal form submission

                let isAllowanceValid = true;
                let allowanceData = [];

                let isDeductionValid = true;
                let deductionData = [];

                // Validate Tax Inputs
                document.querySelectorAll(".allowance-entry").forEach(entry => {
                    const allowanceType = entry.querySelector(".allowance-type");
                    const allowanceAmount = entry.querySelector(".allowance-amount");

                    const type = allowanceType.value.trim();
                    const amount = allowanceAmount.value.trim();

                    // Clear previous errors
                    allowanceType.classList.remove("is-invalid");
                    allowanceAmount.classList.remove("is-invalid");

                    // Find invalid-feedback within the same parent div
                    let allowanceFeedback = allowanceType.parentElement.querySelector(
                        ".invalid-feedback");
                    let amountFeedback = allowanceAmount.parentElement.querySelector(
                        ".invalid-feedback");

                    // Ensure the error messages exist
                    if (!allowanceFeedback) {
                        allowanceFeedback = document.createElement("div");
                        allowanceFeedback.className = "invalid-feedback";
                        allowanceType.parentElement.appendChild(allowanceFeedback);
                    }

                    if (!amountFeedback) {
                        amountFeedback = document.createElement("div");
                        amountFeedback.className = "invalid-feedback";
                        allowanceAmount.parentElement.appendChild(amountFeedback);
                    }

                    // Validation logic
                    if ((type && !amount) || (!type && amount)) {
                        isAllowanceValid = false;
                        if (!type) {
                            allowanceType.classList.add("is-invalid");
                            allowanceFeedback.textContent =
                                "Allowance Type is required if Amount is filled.";
                        }
                        if (!amount) {
                            allowanceAmount.classList.add("is-invalid");
                            amountFeedback.textContent =
                                "Amount is required if Allowance Type is filled.";
                        }
                    } else if (type && amount) {
                        const percentagePattern = /^(0|[1-9]\d*)(\.\d{2})?$/;

                        if (!percentagePattern.test(amount)) {
                            isAllowanceValid = false;
                            allowanceAmount.classList.add("is-invalid");
                            amountFeedback.textContent =
                                "Amount must be a valid number with up to two decimal places (e.g., 1, 5.25, 10.99, 123.00).";
                        } else {
                            allowanceData.push({
                                type,
                                amount
                            });
                        }
                    }
                });

                document.querySelectorAll(".deduction-entry").forEach(entry => {
                    const deductionType = entry.querySelector(".deduction-type");
                    const deductionAmount = entry.querySelector(".deduction-amount");

                    const type = deductionType.value.trim();
                    const amount = deductionAmount.value.trim();

                    // Clear previous errors
                    deductionType.classList.remove("is-invalid");
                    deductionAmount.classList.remove("is-invalid");

                    // Find invalid-feedback within the same parent div
                    let deductionFeedback = deductionType.parentElement.querySelector(
                        ".invalid-feedback");
                    let amountFeedback = deductionAmount.parentElement.querySelector(
                        ".invalid-feedback");

                    // Ensure the error messages exist
                    if (!deductionFeedback) {
                        deductionFeedback = document.createElement("div");
                        deductionFeedback.className = "invalid-feedback";
                        deductionType.parentElement.appendChild(deductionFeedback);
                    }

                    if (!amountFeedback) {
                        amountFeedback = document.createElement("div");
                        amountFeedback.className = "invalid-feedback";
                        deductionAmount.parentElement.appendChild(amountFeedback);
                    }

                    // Validation logic
                    if ((type && !amount) || (!type && amount)) {
                        isAeductionValid = false;
                        if (!type) {
                            deductionType.classList.add("is-invalid");
                            deductionFeedback.textContent =
                                "Deduction Type is required if Amount is filled.";
                        }
                        if (!amount) {
                            deductionAmount.classList.add("is-invalid");
                            amountFeedback.textContent =
                                "Amount is required if Deduction Type is filled.";
                        }
                    } else if (type && amount) {
                        const percentagePattern = /^(0|[1-9]\d*)(\.\d{2})?$/;

                        if (!percentagePattern.test(amount)) {
                            isAeductionValid = false;
                            deductionAmount.classList.add("is-invalid");
                            amountFeedback.textContent =
                                "Amount must be a valid number with up to two decimal places (e.g., 1, 5.25, 10.99, 123.00).";
                        } else {
                            deductionData.push({
                                type,
                                amount
                            });
                        }
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

                if (!isAllowanceValid || !isDeductionValid) {
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
                        formData.append("allowances", JSON.stringify(allowanceData));
                        formData.append("deductions", JSON.stringify(deductionData));
                        formData.append("latitude", latitude);
                        formData.append("longitude", longitude);

                        // Log all form data
                        console.log("Form Data:");
                        for (const [key, value] of formData.entries()) {
                            console.log(`${key}:`, value);
                        }

                        fetch("{{ route('admin.branches.departments.teams.employees.store', ['branchSlug' => $branch->slug, 'departmentSlug' => $department->slug, 'teamSlug' => $team->slug]) }}", {
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
