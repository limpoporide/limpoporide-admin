<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Limpopo Ride</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link href="{{asset('public/admin_asstets/img/ride_hailing.png')}}" rel="icon">
  <link href="{{asset('public/admin_asstets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset('public/admin_asstets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('public/admin_asstets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{asset('public/admin_asstets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{asset('public/admin_asstets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{asset('public/admin_asstets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
  <link href="{{asset('public/admin_asstets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
  <link href="{{asset('public/admin_asstets/vendor/simple-datatables/style.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{asset('public/admin_asstets/css/style.css')}}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<style>
    .cke_notification_warning{
        display:none;
    }
</style>

<body>
    
    
    <!-- ======= Header ======= -->
    @extends('admin.includes.header')
   
    <!-- ======= Sidebar ======= -->
    @extends('admin.includes.sidebar')
    
    @yield('content') 
    
    @extends('admin.includes.footer')
    

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
  <script src="{{asset('public/admin_asstets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/chart.js/chart.umd.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/quill/quill.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/simple-datatables/simple-datatables.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('public/admin_asstets/vendor/php-email-form/validate.js')}}"></script>

  <!-- Template Main JS File -->
  <script src="{{asset('public/admin_asstets/js/main.js')}}"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{asset('public/admin_asstets/js/customjs.js')}}"></script>

</body>

</html>    