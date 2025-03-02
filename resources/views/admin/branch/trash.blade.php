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
    <!-- Required Vendor Styles -->
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}"
        rel="stylesheet">

    <!-- DataTables -->
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css"> --}}

    <!-- Additional Vendor Styles -->
    <link href="{{ asset('assets/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
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
            <!-- row -->
            <div class="container-fluid">
                <div class="form-head page-titles d-flex align-items-center">
                    <div class="me-auto d-lg-block d-block">
                        <h4 class="mb-1">Trashed Branches</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="javascript:void(0)">Branches</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Trashed Branches</a></li>
                        </ol>
                    </div>
                    <a href="javascript:void(0);" class="btn btn-primary rounded light">Refresh</a>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media align-items-center">
                                    <div class="media-body">
                                        <h3 id="totalBranchCount"></h3>
                                        <span class="fs-14 text-black">Total Trashed Branches</span>
                                    </div>
                                    <span class="bg-primary rounded p-3">
                                        <svg width="28" height="28" viewBox="0 0 32 38" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M30.0833 38H1.58333C1.16341 38 0.76068 37.8332 0.463748 37.5363C0.166815 37.2393 0 36.8366 0 36.4167V34.846C0.00454968 32.3984 0.823669 30.022 2.32819 28.0915C3.83271 26.161 5.93704 24.7861 8.30933 24.1838C8.64572 24.1014 8.94519 23.9096 9.1607 23.6385C9.37622 23.3673 9.49557 23.0323 9.5 22.686V21.0932L7.73142 19.3262C7.2884 18.8912 6.93657 18.3723 6.69651 17.7997C6.45645 17.2272 6.33298 16.6125 6.33333 15.9917V9.43984C6.36235 6.99347 7.32801 4.65129 9.03156 2.8953C10.7351 1.13932 13.047 0.103143 15.4913 8.17276e-06C16.7821 -0.00165631 18.0606 0.250939 19.2538 0.743372C20.447 1.23581 21.5315 1.95843 22.4454 2.86999C23.3594 3.78156 24.0848 4.8642 24.5803 6.05611C25.0758 7.24803 25.3317 8.52587 25.3333 9.81667V15.9917C25.3329 16.6169 25.2072 17.2358 24.9638 17.8117C24.7205 18.3876 24.3643 18.909 23.9163 19.3452L22.1667 21.0932V22.686C22.1712 23.0325 22.2908 23.3677 22.5066 23.6389C22.7224 23.91 23.0222 24.1017 23.3589 24.1838C25.7308 24.7867 27.8346 26.1617 29.3388 28.0922C30.8429 30.0226 31.6619 32.3987 31.6667 34.846V36.4167C31.6667 36.8366 31.4999 37.2393 31.2029 37.5363C30.906 37.8332 30.5033 38 30.0833 38ZM3.16667 34.8333H28.5C28.4927 33.091 27.9061 31.4005 26.8326 30.0281C25.7591 28.6556 24.2597 27.6791 22.5704 27.2523C21.5532 26.9949 20.6503 26.4066 20.004 25.58C19.3576 24.7534 19.0045 23.7353 19 22.686V20.4377C19.0001 20.0178 19.167 19.6151 19.4639 19.3183L21.6964 17.0873C21.8445 16.9458 21.9625 16.7758 22.0433 16.5875C22.1241 16.3992 22.1661 16.1966 22.1667 15.9917V9.81667C22.1693 8.06695 21.4812 6.3869 20.252 5.14168C19.0228 3.89645 17.3518 3.1867 15.6022 3.16667C13.9751 3.23184 12.4352 3.91887 11.2998 5.08606C10.1644 6.25326 9.52019 7.81164 9.5 9.43984V15.9917C9.49967 16.1922 9.53942 16.3907 9.61691 16.5756C9.69441 16.7605 9.80808 16.928 9.95125 17.0683L12.2028 19.3167C12.4997 19.6135 12.6666 20.0162 12.6667 20.4361V22.6844C12.6623 23.7335 12.3093 24.7514 11.6633 25.578C11.0173 26.4046 10.1148 26.9931 9.09783 27.2508C7.40797 27.6773 5.90801 28.6539 4.8342 30.0267C3.76039 31.3995 3.17375 33.0905 3.16667 34.8333Z"
                                                fill="white" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="card house-bx">
                            <div class="card-body d-flex">
                                <div class="media align-items-center">
                                    <svg width="55" height="55" viewBox="0 0 88 85" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M77.25 	30.8725V76.25H10.75V30.8725L44 8.70001L77.25 30.8725Z"
                                            fill="url(#paint0_linear)"></path>
                                        <path d="M2 76.25H86V85H2V76.25Z" fill="url(#paint1_linear)"></path>
                                        <path d="M21.25 39.5H42.25V76.25H21.25V39.5Z" fill="url(#paint2_linear)">
                                        </path>
                                        <path d="M52.75 39.5H66.75V64H52.75V39.5Z" fill="url(#paint3_linear)"></path>
                                        <path
                                            d="M87.9424 29.595L84.0574 35.405L43.9999 8.70005L3.94237 35.405L0.057373 29.595L43.9999 0.300049L87.9424 29.595Z"
                                            fill="url(#paint4_linear)"></path>
                                        <path d="M49.25 62.25H70.25V65.75H49.25V62.25Z" fill="url(#paint5_linear)">
                                        </path>
                                        <path d="M52.75 50H66.75V53.5H52.75V50Z" fill="url(#paint6_linear)"></path>
                                        <path
                                            d="M28.25 57C28.25 57.4642 28.0656 57.9093 27.7374 58.2375C27.4092 58.5657 26.9641 58.75 26.5 58.75C26.0359 58.75 25.5908 58.5657 25.2626 58.2375C24.9344 57.9093 24.75 57.4642 24.75 57C24.75 56.5359 24.9344 56.0908 25.2626 55.7626C25.5908 55.4344 26.0359 55.25 26.5 55.25C26.9641 55.25 27.4092 55.4344 27.7374 55.7626C28.0656 56.0908 28.25 56.5359 28.25 57Z"
                                            fill="url(#paint7_linear)"></path>
                                        <defs>
                                            <linearGradient id="paint0_linear" x1="19.255" y1="28.8075"
                                                x2="64.1075" y2="73.6775" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#F9F9DF"></stop>
                                                <stop offset="1" stop-color="#B6BDC6"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint1_linear" x1="2" y1="80.625"
                                                x2="86" y2="80.625" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#3C6DB0"></stop>
                                                <stop offset="1" stop-color="#291F51"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint2_linear" x1="22.9825" y1="40.6025"
                                                x2="37.8575" y2="69.915" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#F0CB49"></stop>
                                                <stop offset="1" stop-color="#E17E43"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint3_linear" x1="52.75" y1="51.75"
                                                x2="66.75" y2="51.75" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#7BC7E9"></stop>
                                                <stop offset="1" stop-color="#3C6DB0"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint4_linear" x1="0.057373" y1="17.8525"
                                                x2="87.9424" y2="17.8525" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#E17E43"></stop>
                                                <stop offset="1" stop-color="#85152E"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint5_linear" x1="784.25" y1="216.25"
                                                x2="1036.25" y2="216.25" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#3C6DB0"></stop>
                                                <stop offset="1" stop-color="#291F51"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint6_linear" x1="570.75" y1="179.5"
                                                x2="682.75" y2="179.5" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#3C6DB0"></stop>
                                                <stop offset="1" stop-color="#291F51"></stop>
                                            </linearGradient>
                                            <linearGradient id="paint7_linear" x1="98.25" y1="195.25"
                                                x2="105.25" y2="195.25" gradientUnits="userSpaceOnUse">
                                                <stop offset="0" stop-color="#E17E43"></stop>
                                                <stop offset="1" stop-color="#85152E"></stop>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="media-body">
                                    <h4 class="fs-22 text-white">INFORMATION</h4>
                                    <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                        eiusmod tempor incididunt </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="table-responsive fs-14">
                            <table class="table display mb-4 dataTablesCard overflow-hidden card-table dataTable"
                                id="branchTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check custom-checkbox ms-2">
                                                <input type="checkbox" class="form-check-input" id="checkAll"
                                                    required="">
                                                <label class="form-check-label" for="checkAll"></label>
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Leader</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                        <th>Updated By</th>
                                        <th>Updated At</th>
                                        <th>Deleted At</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Body End -->

        <!-- Footer Start -->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="http://dexignzone.com/"
                        target="_blank">DexignZone</a> <span class="current-year">2024</span></p>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Supprt Ticket Button Start -->
        <!-- Supprt Ticket Button End -->


    </div>
    <!-- Main Wrapper End -->

    <!-- Footer Scripts Section Start -->

    <!-- Required Vendors -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>

    <!-- Footer Scripts Section End -->

    <script>
        $(document).ready(function() {
            // Initialize datepickers
            $("#fromDate, #toDate").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });

            var currentAjaxRequest = null;

            var dataTable = $('#branchTable').DataTable({
                ajax: {
                    url: "{{ route('admin.branches.trash.data') }}", // Ensure this route is correct
                    type: 'GET',
                    data: function(d) {
                        d.fromDate = $('#fromDate').val();
                        d.toDate = $('#toDate').val();
                        d.token = $('#tokenNo').val();
                    },
                    dataSrc: 'data', // Simplified to expect json.data
                    beforeSend: function(jqXHR) {
                        if (currentAjaxRequest) {
                            currentAjaxRequest.abort();
                        }
                        $('#branchTable').addClass('processing');
                        currentAjaxRequest = jqXHR;
                    },
                    complete: function(jqXHR) {
                        currentAjaxRequest = null;
                        $('#branchTable').removeClass('processing');

                        if (jqXHR.responseJSON) {
                            $('#totalBranchCount').text(jqXHR.responseJSON
                                .recordsTotal); // Use .text() for jQuery
                            console.log("Total Records:", jqXHR.responseJSON.recordsTotal);
                        } else {
                            // console.warn("No responseJSON found");
                        }
                    }
                },
                columns: [{
                        data: "checkbox",
                        orderable: false,
                        className: 'text-center',
                        render: function(data) {
                            return `<div class="form-check custom-checkbox ms-2">
                                <input type="checkbox" class="form-check-input" id="customCheckBox${data}" required>
                                <label class="form-check-label" for="customCheckBox${data}"></label>
                            </div>`;
                        }
                    },
                    {
                        data: "branch_unique_id",
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "name",
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "mobile",
                        className: 'text-center text-nowrap',
                        render: function(data) {
                            return data ? `<a href="tel:${data}">${data}</a>` : '';
                        }
                    },
                    {
                        data: "email",
                        className: 'text-center text-nowrap',
                        render: function(data) {
                            return data ? `<a href="mailto:${data}">${data}</a>` : '';
                        }
                    },
                    {
                        data: "address",
                        orderable: false,
                        className: 'text-center text-nowrap',
                        render: function(data, type, row) {
                            const maxLength = 25;
                            const truncatedAddress = data.length > maxLength ? data.substring(0,
                                maxLength) + '...' : data;

                            // Check if both latitude and longitude are available
                            let googleMapsLink = "";
                            if (row.latitude && row.longitude) {
                                const googleMapsUrl =
                                    `https://www.google.com/maps?q=${row.latitude},${row.longitude}`;
                                const googleMapsIcon =
                                    "{{ asset('assets/images/iconly/action/google-maps-locator.png') }}";
                                googleMapsLink = `
                <a href="${googleMapsUrl}" target="_blank" class="google-maps-link">
                    <img src="${googleMapsIcon}" alt="Google Maps" style="width: 20px; height: 20px; margin-left: 5px;">
                </a>
            `;
                            }

                            return `<div class="address-container">
            <span class="address-text">${truncatedAddress}</span>
            ${data.length > maxLength ? '<br><button class="btn btn-link view-more">View More</button>' : ''}
            ${googleMapsLink}
            <div class="full-address" style="display: none;">
                <p>${data}</p>
                ${data.length > maxLength ? '<button class="btn btn-link view-less">View Less</button>' : ''}
            </div>
        </div>`;
                        }
                    },
                    {
                        data: "leader",
                        orderable: false,
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "creator",
                        orderable: false,
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "created_at",
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "updator",
                        orderable: false,
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "updated_at",
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "deleted_at",
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: "actions",
                        orderable: false,
                        className: 'text-center',
                        render: function(data) {
                            return `<div class="dropdown ms-auto">
                                <div class="btn-link" data-bs-toggle="dropdown">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 12C11 12.5523 11.4482 13 12 13C12.5528 13 13 12.5523 13 12C13 11.4477 12.5528 11 12 11C11.4482 11 11 11.4477 11 12Z" stroke="#3E4954" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M18 12C18 12.5523 18.4482 13 19 13C19.5528 13 20 12.5523 20 12C20 11.4477 19.5528 11 19 11C18.4482 11 18 11.4477 18 12Z" stroke="#3E4954" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M4 12C4 12.5523 4.4482 13 5 13C5.55277 13 6 12.5523 6 12C6 11.4477 5.55277 11 5 11C4.4482 11 4 11.4477 4 12Z" stroke="#3E4954" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item text-black restore-branch" href="javascript:void(0);" data-url="${data.restore}">Restore</a>
                                    <a class="dropdown-item text-black delete-branch" href="javascript:void(0);" data-url="${data.delete}">Delete</a>
                                </div>
                            </div>`;
                        }
                    }
                ],
                scrollX: false,
                autoWidth: false,
                responsive: false,
                processing: true,
                serverSide: true,
                lengthMenu: [10, 25, 50, 100, 200, 500],
                pageLength: 25,
                language: {
                    paginate: {
                        previous: '<i class="fa fa-angle-double-left"></i>',
                        next: '<i class="fa fa-angle-double-right"></i>'
                    },
                    emptyTable: "<strong>No branches found in the trash.</strong>", // Custom empty table message
                    zeroRecords: "<strong>No matching records found.</strong>" // Custom message when search has no matches
                },
                fixedHeader: true,
                scrollCollapse: true,
                initComplete: function() {
                    this.api().columns.adjust(); // Adjust column widths after initialization
                }
            });

            // Address View More/View Less functionality
            $('#branchTable').on('click', '.view-more', function() {
                $(this).closest('.address-container').find('.address-text').hide();
                $(this).closest('.address-container').find('.full-address').show();
                $(this).hide();
                $(this).closest('.address-container').find('.view-less').show();
            });

            $('#branchTable').on('click', '.view-less', function() {
                $(this).closest('.full-address').hide();
                $(this).closest('.address-container').find('.address-text').show();
                $(this).closest('.address-container').find('.view-more').show();
                $(this).hide();
            });

            // Checkbox Select All Functionality
            $('#checkAll').on('click', function() {
                $('.form-check-input').prop('checked', this.checked);
            });

            $('#branchTable').on('click', '.form-check-input', function() {
                if ($('.form-check-input:checked').length === $('.form-check-input').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            });

            // Delete Branch with SweetAlert Confirmation
            $('#branchTable').on('click', '.delete-branch', function(e) {
                e.preventDefault();

                let deleteUrl = $(this).data('url'); // Get delete URL from data attribute

                Swal.fire({
                    title: "Are you absolutely sure?",
                    text: "This action is irreversible! The branch will be permanently deleted and cannot be restored.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Delete Permanently",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}" // Ensure CSRF token is included
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Deleted Permanently!",
                                    text: "The branch has been removed forever and cannot be recovered.",
                                    icon: "success",
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                                $('#branchTable').DataTable().ajax
                                    .reload(); // Reload table
                            },
                            error: function() {
                                Swal.fire({
                                    title: "Error!",
                                    text: "An unexpected error occurred. The branch may not have been deleted. Please try again.",
                                    icon: "error",
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            $('#branchTable').on('click', '.restore-branch', function(e) {
                e.preventDefault();

                let restoreUrl = $(this).data('url'); // Get restore URL from data attribute

                Swal.fire({
                    title: "Restore Branch?",
                    text: "This will bring back the branch and make it active again.",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Restore",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: restoreUrl,
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}" // Ensure CSRF token is included
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Restored Successfully!",
                                    text: "The branch has been successfully restored.",
                                    icon: "success",
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                                $('#branchTable').DataTable().ajax
                                    .reload(); // Reload table
                            },
                            error: function() {
                                Swal.fire({
                                    title: "Error!",
                                    text: "An unexpected error occurred. The branch could not be restored. Please try again.",
                                    icon: "error",
                                    timer: 2500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });



            // Reload table when filter button is clicked
            $('#filterButton').click(function() {
                dataTable.ajax.reload();
            });
        });
    </script>
</body>

</html>
