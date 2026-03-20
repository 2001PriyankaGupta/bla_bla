@yield('css')
@stack('styles')
<!-- Bootstrap Css -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@5.9.55/css/materialdesignicons.min.css">
<link href="{{ URL::asset('build/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ URL::asset('build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{ URL::asset('build/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<style>
    :root {
        --bs-primary: #198754 !important;
        --bs-primary-rgb: 25, 135, 84 !important;
        --primary-green: #198754;
    }
    .bg-primary { background-color: var(--primary-green) !important; }
    .btn-primary { background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; }
    .btn-primary:hover { background-color: #157347 !important; border-color: #146c43 !important; }
    .text-primary { color: var(--primary-green) !important; }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link { background-color: var(--primary-green) !important; }
    .form-check-input:checked { background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; }
    
    /* Sidebar branding if it uses primary */
    #sidebar-menu ul li a:hover, #sidebar-menu ul li a.active { color: var(--primary-green) !important; }
    #sidebar-menu ul li a:hover i, #sidebar-menu ul li a.active i { color: var(--primary-green) !important; }
    
    /* Card headers */
    .card-title { color: var(--primary-green) !important; }
</style>
