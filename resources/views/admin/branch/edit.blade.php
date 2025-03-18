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
                        <h4 class="mb-1 fw-bold">Edit Branch: {{ $branch->name }}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.branches.index') }}">Branches</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.branches.show', ['branchSlug' => $branch->slug]) }}">{{ $branch->name }}</a>
                                </li>
                                <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
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
                                <form action="{{ route('admin.branches.edit', ['branchSlug' => $branch->slug]) }}"
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
                                                placeholder="Enter name" value="{{ old('name', $branch->name) }}"
                                                required>
                                            <div class="invalid-feedback">Name is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label fw-bold">Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                placeholder="Enter email" value="{{ old('email', $branch->email) }}"
                                                required>
                                            <div class="invalid-feedback">Valid email is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mobile" class="form-label fw-bold">Mobile <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="mobile" name="mobile" class="form-control"
                                                placeholder="Enter mobile"
                                                value="{{ old('mobile', $branch->mobile) }}" required>
                                            <div class="invalid-feedback">Mobile number is required.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="date_of_start" class="form-label fw-bold">Date of
                                                Start <span class="text-danger">*</span></label>
                                            <input id="date_of_start" name="date_of_start"
                                                class="form-control bt-datepicker" required
                                                placeholder="Select Date of Staart" data-autoclose="true"
                                                data-today-highlight="true" data-default-date="false"
                                                value="{{ old('date_of_start', $branch->date_of_start) }}" required>
                                            <div class="invalid-feedback">Start date is required.</div>

                                        </div>
                                        <!-- Type Dropdown -->
                                        <div class="col-md-4">
                                            <label for="type" class="form-label fw-bold">Type <span
                                                    class="text-danger">*</span></label>
                                            <select id="type" name="type" class="form-select" required>
                                                <option value="">Select Type</option>
                                                <option value="head_office"
                                                    {{ old('type', $branch->type) == 'head_office' ? 'selected' : '' }}>
                                                    Head Office
                                                </option>
                                                <option value="regional"
                                                    {{ old('type', $branch->type) == 'regional' ? 'selected' : '' }}>
                                                    Regional
                                                </option>
                                                <option value="franchise"
                                                    {{ old('type', $branch->type) == 'franchise' ? 'selected' : '' }}>
                                                    Franchise
                                                </option>
                                                <option value="sub_branch"
                                                    {{ old('type', $branch->type) == 'sub_branch' ? 'selected' : '' }}>
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
                                                <option value="active"
                                                    {{ old('status', $branch->status) == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', $branch->status) == 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                                <option value="suspended"
                                                    {{ old('status', $branch->status) == 'suspended' ? 'selected' : '' }}>
                                                    Suspended</option>
                                                <option value="archived"
                                                    {{ old('status', $branch->status) == 'archived' ? 'selected' : '' }}>
                                                    Archived</option>
                                            </select>
                                            <div class="invalid-feedback">Status is required.</div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="description" class="form-label fw-bold">Description</label>
                                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Enter description">{{ old('description', $branch->description) }}</textarea>
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
                                                class="form-control" placeholder="Enter address line 1"
                                                value="{{ old('address_line1', $branch->address_line1) }}" required
                                                maxlength="100">
                                            <div class="invalid-feedback">Primary address line is required.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address_line2" class="form-label fw-bold">Address Line
                                                2</label>
                                            <input type="text" id="address_line2" name="address_line2"
                                                class="form-control" placeholder="Enter address line 2"
                                                maxlength="100"
                                                value="{{ old('address_line2', $branch->address_line2) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="country" class="form-label fw-bold">Country <span
                                                    class="text-danger">*</span></label>
                                            <select id="country" name="country_id"
                                                class="form-select dropdown-select" required>
                                                <option value="">Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ old('country_id', $branch->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
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
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ old('state_id', $branch->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">State is required.</div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="city" class="form-label fw-bold">City <span
                                                    class="text-danger">*</span></label>
                                            <select id="city" name="city_id" class="form-select dropdown-select"
                                                required>
                                                <option value="">Select City</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->id }}"
                                                        {{ old('city_id', $branch->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">City is required.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="postal_code" class="form-label fw-bold">Postal Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="postal_code" name="postal_code"
                                                class="form-control" placeholder="Enter postal code" required
                                                maxlength="10"
                                                value="{{ old('postal_code', $branch->postal_code) }}">
                                            <div class="invalid-feedback">Postal code is required.</div>
                                        </div>

                                        <!-- Latitude & Longitude Fields -->
                                        <div class="col-md-3">
                                            <label for="branch_latitude" class="form-label fw-bold">Latitude <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" id="branch_latitude" name="branch_latitude"
                                                class="form-control" placeholder="Enter latitude" required
                                                step="any" min="-90" max="90"
                                                value="{{ old('latitude', $branch->latitude) }}">
                                            <div class="invalid-feedback">Please enter a valid latitude.</div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="branch_longitude" class="form-label fw-bold">Longitude <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" id="branch_longitude" name="branch_longitude"
                                                class="form-control" placeholder="Enter longitude" required
                                                step="any" min="-180" max="180"
                                                value="{{ old('longitude', $branch->longitude) }}">
                                            <div class="invalid-feedback">Please enter a valid longitude.</div>
                                        </div>

                                        <!-- Get Coordinates Button -->
                                        <div class="col-md-12 text-center mt-3">
                                            <button type="button" class="btn btn-primary" id="getCoordinates">
                                                <span
                                                    class="branchCoordinatesBtnSpinner spinner-border spinner-border-sm me-2 d-none"
                                                    role="status" aria-hidden="true" id="loadingSpinner"></span>
                                                <i class="fas fa-map-marker-alt"></i> <span
                                                    id="branch-coordinates-btn-txt">Get Coordinates</span>
                                            </button>
                                            <p id="geoError" class="text-danger mt-2"></p>
                                        </div>

                                        <!-- Section: Operating Hours -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Operating Hours</h5>
                                        </div>
                                        @php
                                            $operatingHours = json_decode($branch->operating_hours, true) ?? [];
                                        @endphp
                                        <!-- 24/7 Open Checkbox -->
                                        <div class="col-md-12">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="open_24_7"
                                                    name="operating_hours[all][open_24_7]" value="1"
                                                    {{ !empty($operatingHours['all']['open_24_7']) && $operatingHours['all']['open_24_7'] == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="open_24_7">Open 24/7
                                                    (Applies to All Days)</label>
                                            </div>
                                        </div>

                                        <div class="operating-hours-container {{ !empty($operatingHours['all']['open_24_7']) && $operatingHours['all']['open_24_7'] == 1 ? 'd-none' : '' }}">
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Set Operating Hours by Day</label>
                                                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                    @php
                                                        $dayKey = strtolower($day);
                                                        $start = $operatingHours[$dayKey]['start'] ?? '10:00';
                                                        $end = $operatingHours[$dayKey]['end'] ?? '19:00';
                                                        $closed =
                                                            isset($operatingHours[$dayKey]['closed']) &&
                                                            $operatingHours[$dayKey]['closed']
                                                                ? 'checked'
                                                                : '';
                                                    @endphp

                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-3">
                                                            <label
                                                                class="form-label fw-bold">{{ $day }}</label>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group clockpicker"
                                                                data-placement="bottom" data-align="top"
                                                                data-autobtn-close="true">
                                                                <input type="text" class="form-control"
                                                                    name="operating_hours[{{ $dayKey }}][start]"
                                                                    value="{{ $start }}"
                                                                    placeholder="Start Time">
                                                                <span class="input-group-text"><i
                                                                        class="far fa-clock"></i></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="input-group clockpicker"
                                                                data-placement="bottom" data-align="top"
                                                                data-autobtn-close="true">
                                                                <input type="text" class="form-control"
                                                                    name="operating_hours[{{ $dayKey }}][end]"
                                                                    value="{{ $end }}"
                                                                    placeholder="End Time">
                                                                <span class="input-group-text"><i
                                                                        class="far fa-clock"></i></span>
                                                            </div>
                                                        </div>

                                                        <!-- Closed Checkbox Centered -->
                                                        <div
                                                            class="col-md-3 d-flex align-items-center justify-content-center">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="operating_hours[{{ $dayKey }}][closed]"
                                                                    value="1" id="closed_{{ $dayKey }}"
                                                                    {{ $closed }}>
                                                                <label class="form-check-label"
                                                                    for="closed_{{ $dayKey }}">Closed</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Section: Social Media Links -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">Social Media Links</h5>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Add Social Media Profiles</label>
                                            <div id="socialContainer">
                                                @php
                                                    $socialLinks = json_decode($branch->social_links, true) ?? [];
                                                @endphp

                                                @foreach ($socialLinks as $index => $socialLink)
                                                    <div class="row g-2 mb-2 social-entry">
                                                        <div class="col-md-4">
                                                            <select class="form-select dropdown-select"
                                                                name="social_links[{{ $index }}][platform]">
                                                                <option value="">Select Platform</option>
                                                                <option value="facebook"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'facebook' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/facebook.png') }}">
                                                                    Facebook
                                                                </option>
                                                                <option value="linkedin"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'linkedin' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/linkedin.png') }}">
                                                                    LinkedIn
                                                                </option>
                                                                <option value="twitter"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'twitter' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/twitter.png') }}">
                                                                    Twitter
                                                                </option>
                                                                <option value="instagram"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'instagram' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/instagram.png') }}">
                                                                    Instagram
                                                                </option>
                                                                <option value="youtube"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'youtube' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/youtube.png') }}">
                                                                    YouTube
                                                                </option>
                                                                <option value="telegram"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'telegram' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/telegram.png') }}">
                                                                    Telegram
                                                                </option>
                                                                <option value="snapchat"
                                                                    {{ old("social_links.$index.platform", $socialLink['platform'] ?? '') == 'snapchat' ? 'selected' : '' }}
                                                                    data-image="{{ asset('assets/images/iconly/social/circle/snapchat.png') }}">
                                                                    Snapchat
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="url" class="form-control"
                                                                name="social_links[{{ $index }}][url]"
                                                                value="{{ old("social_links.$index.url", $socialLink['url'] ?? '') }}"
                                                                placeholder="Enter URL (e.g., https://facebook.com/yourpage)">
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            @if ($index == 0)
                                                                <button type="button" id="addSocialBtn"
                                                                    class="btn p-2 rounded-circle">
                                                                    <img src="{{ asset('assets/images/iconly/action/plus.png') }}"
                                                                        alt="Add" class="img-fluid"
                                                                        style="max-width: 20px;">
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn remove-social p-2 rounded-circle">
                                                                    <img src="{{ asset('assets/images/iconly/action/remove.png') }}"
                                                                        alt="Delete" class="img-fluid"
                                                                        style="max-width: 20px;">
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if (empty($socialLinks))
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
                                                @endif
                                            </div>
                                        </div>


                                        <!-- Section: Tax Configuration -->
                                        <div class="col-12 mt-4">
                                            <h5 class="text-primary fw-bold mb-3">TAX Configuration</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="gstin" class="form-label fw-bold">GSTIN</label>
                                            <input type="text" id="gstin" name="gstin" class="form-control"
                                                placeholder="Enter GSTIN"
                                                value="{{ old('gstin', $branch->gstin ?? '') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Tax Details</label>
                                            <div id="taxContainer">
                                                @php
                                                    $taxDetails = json_decode($branch->tax_details, true) ?? [];
                                                @endphp

                                                @foreach ($taxDetails as $index => $tax)
                                                    <div class="row g-2 mb-2 tax-entry">
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control tax-title"
                                                                name="tax_title[]"
                                                                value="{{ old('tax_title.' . $index, $tax['title'] ?? '') }}"
                                                                placeholder="Tax Title (e.g., CGST)">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="number" class="form-control tax-percentage"
                                                                name="tax_percentage[]"
                                                                value="{{ old('tax_percentage.' . $index, $tax['percentage'] ?? '') }}"
                                                                placeholder="Percentage (e.g., 9)">
                                                        </div>
                                                        <div class="col-md-2 text-center">
                                                            @if ($index == 0)
                                                                <button type="button" id="addTaxBtn"
                                                                    class="btn p-2 rounded-circle">
                                                                    <img src="{{ asset('assets/images/iconly/action/plus.png') }}"
                                                                        alt="Add" class="img-fluid"
                                                                        style="max-width: 20px;">
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn btn-danger p-2 rounded-circle removeTaxBtn">
                                                                    <img src="{{ asset('assets/images/iconly/action/minus.png') }}"
                                                                        alt="Remove" class="img-fluid"
                                                                        style="max-width: 20px;">
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if (empty($taxDetails))
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
                                                @endif
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
                                                    name="use_branch_smtp_credentials"
                                                    {{ $branch->use_branch_smtp_credentials ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold"
                                                    for="use_branch_smtp_credentials">Use Branch SMTP
                                                    Credentials</label>
                                            </div>
                                        </div>
                                        <div
                                            class="smtpFields {{ $branch->use_branch_smtp_credentials ? '' : 'd-none' }}">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label for="smtp_host" class="form-label fw-bold">SMTP Host <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="smtp_host" name="smtp_host"
                                                        class="form-control" placeholder="Enter SMTP host"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}
                                                        value="{{ old('smtp_host', $branch->smtp_host ?? '') }}">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Host.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_port" class="form-label fw-bold">SMTP Port <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="smtp_port" name="smtp_port"
                                                        class="form-control" placeholder="Enter SMTP port"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}
                                                        value="{{ old('smtp_port', $branch->smtp_port ?? '') }}">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Port.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_username" class="form-label fw-bold">SMTP
                                                        Username <span class="text-danger">*</span></label>
                                                    <input type="text" id="smtp_username" name="smtp_username"
                                                        class="form-control" placeholder="Enter SMTP username"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}
                                                        value="{{ old('smtp_username', $branch->smtp_username ?? '') }}">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Username.
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_password" class="form-label fw-bold">SMTP
                                                        Password <span class="text-danger">*</span></label>
                                                    <input type="password" id="smtp_password" name="smtp_password"
                                                        class="form-control" placeholder="Enter SMTP password"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}
                                                        value="{{ old('smtp_password', $branch->smtp_password ?? '') }}">
                                                    <div class="invalid-feedback">Please enter a valid SMTP Password.
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_encryption" class="form-label fw-bold">SMTP
                                                        Encryption <span class="text-danger">*</span></label>
                                                    <select id="smtp_encryption" name="smtp_encryption"
                                                        class="form-select dropdown-select"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}>
                                                        <option value="">Select Encryption</option>
                                                        <option value="tls"
                                                            {{ old('smtp_encryption', $branch->smtp_encryption ?? '') == 'tls' ? 'selected' : '' }}>
                                                            TLS</option>
                                                        <option value="ssl"
                                                            {{ old('smtp_encryption', $branch->smtp_encryption ?? '') == 'ssl' ? 'selected' : '' }}>
                                                            SSL</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a valid SMTP Encryption
                                                        type.</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_from_email" class="form-label fw-bold">Sender
                                                        Email <span class="text-danger">*</span></label>
                                                    <input type="email" id="smtp_from_email" name="smtp_from_email"
                                                        class="form-control" placeholder="Enter sender email"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}
                                                        value="{{ old('smtp_from_email', $branch->smtp_from_email ?? '') }}">
                                                    <div class="invalid-feedback">Please enter a valid Sender Email.
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="smtp_from_name" class="form-label fw-bold">Sender Name
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" id="smtp_from_name" name="smtp_from_name"
                                                        class="form-control" placeholder="Enter sender name"
                                                        {{ $branch->use_branch_smtp_credentials ? 'required' : '' }}
                                                        value="{{ old('smtp_from_name', $branch->smtp_from_name ?? '') }}">
                                                    <div class="invalid-feedback">Please enter a valid Sender Name.
                                                    </div>
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
    <script src="{{ asset('assets/js/sisodia-dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/custom-2.js') }}"></script>

    <!-- JavaScript for Geolocation -->
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

        document.getElementById("getCoordinates").addEventListener("click", function() {
            let latInput = document.getElementById("branch_latitude");
            let lngInput = document.getElementById("branch_longitude");
            let geoError = document.getElementById("geoError");

            // Show spinner while fetching location
            toggleButton("#submitBtn", {
                textSelector: "#branch-coordinates-btn-txt",
                oldText: "Get Coordinates",
                newText: "Getting Coordinates...",
                spinnerSelector: ".branchCoordinatesBtnSpinner"
            }, true);

            getCurrentLocation(
                (location) => {
                    const {
                        latitude,
                        longitude
                    } = location;


                    latInput.value = latitude;
                    lngInput.value = longitude;
                    geoError.textContent = "";

                    toggleButton("#submitBtn", {
                        textSelector: "#branch-coordinates-btn-txt",
                        oldText: "Get Coordinates",
                        newText: "Getting Coordinates...",
                        spinnerSelector: ".branchCoordinatesBtnSpinner"
                    }, false);

                },
                (errorMessage) => {
                    geoError.textContent = errorMessage;
                    toggleButton("#submitBtn", {
                        textSelector: "#branch-coordinates-btn-txt",
                        oldText: "Get Coordinates",
                        newText: "Getting Coordinates...",
                        spinnerSelector: ".branchCoordinatesBtnSpinner"
                    }, false);
                }
            );
        });

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
                smtpFields.forEach(function(field) {
                    field.classList.add('d-none');

                    // Remove the 'required' attribute from all inputs and selects inside the field
                    field.querySelectorAll('input, select').forEach(function(input) {
                        input.removeAttribute('required');
                    });
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
                        <option value="facebook" data-image="{{ asset('assets/images/iconly/social/circle/facebook.png') }}">Facebook</option>
                        <option value="linkedin" data-image="{{ asset('assets/images/iconly/social/circle/linkedin.png') }}">LinkedIn</option>
                        <option value="twitter" data-image="{{ asset('assets/images/iconly/social/circle/twitter.png') }}">Twitter</option>
                        <option value="instagram" data-image="{{ asset('assets/images/iconly/social/circle/instagram.png') }}">Instagram</option>
                        <option value="youtube" data-image="{{ asset('assets/images/iconly/social/circle/youtube.png') }}">YouTube</option>
                        <option value="telegram" data-image="{{ asset('assets/images/iconly/social/circle/telegram.png') }}">Telegram</option>
                        <option value="snapchat" data-image="{{ asset('assets/images/iconly/social/circle/snapchat.png') }}">Snapchat</option>
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

                let isValid = true;
                const taxData = [],
                    socialData = [];

                const validateFields = (entries, selectors, errorMessages, dataStore, fieldKeys) => {
                    entries.forEach(entry => {
                        const [field1, field2] = selectors.map(sel => entry.querySelector(sel));
                        const [error1, error2] = errorMessages.map(msg => field1.parentElement
                            .querySelector(".invalid-feedback") || createErrorElement(
                                field1, msg));

                        field1.classList.remove("is-invalid");
                        field2.classList.remove("is-invalid");

                        const val1 = field1.value.trim(),
                            val2 = field2.value.trim();

                        if ((val1 && !val2) || (!val1 && val2)) {
                            isValid = false;
                            if (!val1) showError(field1, error1, errorMessages[0]);
                            if (!val2) showError(field2, error2, errorMessages[1]);
                        } else if (val1 && val2) {
                            if (selectors.includes(".tax-percentage") &&
                                !/^(0|[1-9]\d*)(\.\d{1,2})?$/.test(val2)) {
                                showError(field2, error2,
                                    "Invalid percentage format (e.g., 1, 5.25, 10.99).");
                                isValid = false;
                            } else {
                                // ✅ Dynamically set the index keys based on the provided fieldKeys
                                dataStore.push({
                                    [fieldKeys[0]]: val1,
                                    [fieldKeys[1]]: val2
                                });
                            }
                        }
                    });
                };

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

                // **Run validation with correct field keys**
                validateFields(document.querySelectorAll(".tax-entry"), [".tax-title", ".tax-percentage"], [
                    "Tax Title is required.", "Percentage is required."
                ], taxData, ["title",
                    "percentage"
                ]); // ✅ Tax Data stored as { title: val1, percentage: val2 }

                validateFields(document.querySelectorAll(".social-entry"), [".dropdown-select",
                    ".form-control"
                ], [
                    "Platform is required.", "Profile URL is required."
                ], socialData, ["platform",
                    "url"
                ]); // ✅ Social Data stored as { platform: val1, url: val2 }

                // Validate Mobile Number
                const mobileInput = document.getElementById("mobile");
                const mobilePattern = /^\+?[0-9\s-]{10,20}$/; // ✅ Fixed regex
                const mobileError = mobileInput.parentElement.querySelector(".invalid-feedback") ||
                    createErrorElement(mobileInput, "Invalid mobile number.");

                mobileInput.classList.remove("is-invalid");
                if (!mobilePattern.test(mobileInput.value.trim())) {
                    showError(mobileInput, mobileError, "Please enter a valid mobile number.");
                }

                // Log form validity status
                if (!form.checkValidity() || !isValid) {

                    form.classList.add("was-validated");

                    // Manually add .is-invalid class to invalid inputs
                    document.querySelectorAll("input:invalid, select:invalid, textarea:invalid").forEach((
                        input) => {
                        input.classList.add("is-invalid");
                    });

                    // Check for the first invalid field
                    const firstInvalidField = document.querySelector(".is-invalid");

                    if (firstInvalidField) {
                        firstInvalidField.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                        firstInvalidField.focus();
                    }

                    return;
                }

                toggleButton("#submitBtn", {
                    textSelector: "#submit-btn-txt",
                    oldText: "Submit",
                    newText: "Submitting...",
                    spinnerSelector: ".submitBtnSpinner"
                }, true);

                // Get the 'use_branch_smtp_credentials' checkbox value
                const useBranchCredentials = document.getElementById("use_branch_smtp_credentials")
                    .checked ? 1 :
                    0;

                getCurrentLocation(
                    (location) => {
                        const {
                            latitude,
                            longitude
                        } = location;

                        const formData = new FormData(form);
                        formData.set("use_branch_smtp_credentials", useBranchCredentials);
                        formData.set("tax_details", JSON.stringify(taxData));
                        formData.set("social_links", JSON.stringify(socialData));
                        formData.set("latitude", latitude);
                        formData.set("longitude", longitude);
                        formData.set("operating_hours", JSON.stringify(getOperatingHours()));

                        fetch("{{ route('admin.branches.edit', ['branchSlug' => $branch->slug]) }}", {
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
                                        firstInput?.scrollIntoView({
                                            behavior: "smooth",
                                            block: "center"
                                        }).focus();
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Oops...",
                                            text: defaultMessage || "Something went wrong!"
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
