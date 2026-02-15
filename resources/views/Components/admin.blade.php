@props(['title' => 'MediFlow'])

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | MediFlow</title>
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendors/feather/feather.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/ti-icons/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/typicons/typicons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/simple-line-icons/css/simple-line-icons.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
    <!-- Page-specific CSS -->
    @stack('styles')
    <!-- App CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}" />
    <!-- Print Styles -->
    <style>
      @media print {
        /* Hide everything except the card body */
        .navbar, .sidebar, .content-wrapper > h3, .footer, 
        .btn, button, .card-header, .breadcrumb, 
        .page-body-wrapper .sidebar-offcanvas {
          display: none !important;
        }
        
        /* Remove container constraints */
        .container-scroller, .page-body-wrapper, .main-panel, .content-wrapper {
          width: 100% !important;
          margin: 0 !important;
          padding: 0 !important;
          max-width: 100% !important;
        }
        
        /* Card styling for print */
        .card {
          border: none !important;
          box-shadow: none !important;
          margin: 0 !important;
        }
        
        .card-body {
          padding: 20px !important;
        }
        
        /* Ensure tables print properly */
        table {
          page-break-inside: auto;
        }
        
        tr {
          page-break-inside: avoid;
          page-break-after: auto;
        }
        
        /* Remove background colors */
        body {
          background: white !important;
        }
        
        .table-light {
          background-color: #f8f9fa !important;
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
        }
        
        /* Ensure badges show in print */
        .badge {
          border: 1px solid #000 !important;
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
        }
      }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <x-nav-bar />
      <div class="container-fluid page-body-wrapper">
        <x-side-bar />
        <div class="main-panel">
          <div class="content-wrapper">
            <h3 class="mb-4">{{ $title }}</h3>
            {{ $slot }}
          </div>
          <x-footer />
        </div>
      </div>
    </div>
    <!-- Vendor JS -->
    <script src="{{asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
    <!-- App JS -->
    <script src="{{asset('assets/js/off-canvas.js')}}"></script>
    <script src="{{asset('assets/js/template.js')}}"></script>
    <script src="{{asset('assets/js/settings.js')}}"></script>
    <script src="{{asset('assets/js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('assets/js/todolist.js')}}"></script>
    <!-- Page-specific JS -->
    @stack('scripts')
  </body>
</html>