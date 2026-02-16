<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from medico-bootstrap-admin.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 21 Aug 2025 10:12:10 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>

	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Doctore - Dashbord </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('backend/vendor/jqvmap/css/jqvmap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/vendor/chartist/css/chartist.min.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">

    <!-- Datatable -->
    <link href="{{ asset('backend/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="index.html" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('backend/images/logo-white.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('backend/images/logo-text-white.png') }}" alt="">
                <img class="brand-title" src="{{ asset('backend/images/logo-text-white.png') }}" alt="">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        @include('backend.doctor.doctor_body.doctor_header')
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('backend.doctor.doctor_body.doctor_sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->

		
		
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            @yield('doctor')
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->
        @include('backend.doctor.doctor_body.doctor_footer')
        <!--**********************************
            Footer end
        ***********************************-->

		<!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('backend/vendor/global/global.min.js') }} "></script>
    <script src="{{ asset('backend/js/deznav-init.js') }}"></script>	
	<script src="{{ asset('backend/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('backend/js/custom.min.js') }}"></script>

    <!-- Datatable -->
    <script src="{{ asset('backend/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/js/plugins-init/datatables.init.js') }}"></script>

    <!-- Vectormap -->
    <script src="{{ asset('backend/vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/gaugeJS/dist/gauge.min.js') }}"></script>

	<!-- Counter Up -->
    <script src="{{ asset('backend/vendor/waypoints/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/jquery.counterup/jquery.counterup.min.js') }}"></script>

	<!-- Demo scripts -->
    <script src="{{ asset('backend/js/dashboard/dashboard.js') }}"></script>

	<!-- Svganimation scripts -->
    <script src="{{ asset('backend/vendor/svganimation/vivus.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/svganimation/svg.animation.js') }}"></script>
	
</body>

<!-- Mirrored from medico-bootstrap-admin.vercel.app/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 21 Aug 2025 10:12:56 GMT -->
</html>