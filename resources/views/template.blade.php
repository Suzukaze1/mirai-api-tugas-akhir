<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Antrian</title>

    <!-- DataTables -->
    <link href="{{asset("assets/plugins/datatables/dataTables.bootstrap4.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{asset("assets/plugins/datatables/buttons.bootstrap4.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{asset("assets/plugins/datatables/responsive.bootstrap4.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{asset("assets/plugins/datatables/select.bootstrap4.min.css")}}" rel="stylesheet" type="text/css" />

    <!--Morris Chart CSS -->
	<link rel="stylesheet" href="{{asset('assets/plugins/morris/morris.css')}}">

    <!-- App css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/icons.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" type="text/css" />

    <script src="{{asset('assets/js/modernizr.min.js')}}"></script>

    <style>
        .div-1 {
            background-color: #ABBAEA;
        }
    </style>

</head>
<body class="fixed-left">
    <header>
        <!-- Begin page -->
        <div id="wrapper">
            
            <!-- Top Bar Start -->
            <div class="topbar">

                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="/home" class="logo"><span><span>MIRAI</span></span><i class="mdi mdi-layers"></i></a>
                   
                </div>

            </div>
            <!-- Top Bar End -->

            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <ul>
                        	<li class="text-muted menu-title">Menu</li>

                            <li>
                                <a href="/home" class="waves-effect"><i class="mdi mdi-home"></i> <span> Home </span> </a>
                            </li>

                            <li>
                                <a href="/list-pasien" class="waves-effect"><i class="mdi mdi-book-outline"></i> <span> List Pasien </span> </a>
                            </li>
                            
                            <li>
                                <a href="/logout" onclick="keluar()" class="waves-effect"><i class="mdi mdi-logout"></i> <span> Keluar </span> </a>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>

                </div>

            </div>
            <!-- Left Sidebar End -->
    
           
    
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->

                <!-- bagian konten blog -->
                    @yield('konten')
                <!-- End content -->

                <br>
                <br>           

                <footer class="footer text-right">
                    Ruu
                </footer>

            </div>

            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->

            <!-- Right Sidebar -->
            <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="mdi mdi-close-circle-outline"></i>
                </a>
                <h4 class="">Notifications</h4>
                <div class="notification-list nicescroll">
                    <ul class="list-group list-no-border user-list">
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="../assets/images/users/avatar-2.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">Michael Zenaty</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-info">
                                    <i class="mdi mdi-account"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Signup</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">5 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-pink">
                                    <i class="mdi mdi-comment"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">New Message received</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="avatar">
                                    <img src="../assets/images/users/avatar-3.jpg" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name">James Anderson</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">2 days ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item active">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-warning">
                                    <i class="mdi mdi-settings"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name">Settings</span>
                                    <span class="desc">There are new settings available</span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- /Right-bar -->
        </div>

        
        <!-- jQuery  -->

        <script>
            function keluar(){
                var con = confirm("Apakah anda ingin keluar?")
                if(con === true){
                    location.href = "/";
                }
            }
        </script>
        
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script src="{{asset('assets/js/popper.min.js')}}"></script>
        <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('assets/js/detect.js')}}"></script>
        <script src="{{asset('assets/js/fastclick.js')}}"></script>
        <script src="{{asset('assets/js/jquery.blockUI.js')}}"></script>
        <script src="{{asset('assets/js/waves.js')}}"></script>
        <script src="{{asset('assets/js/jquery.nicescroll.js')}}"></script>
        <script src="{{asset('assets/js/jquery.slimscroll.js')}}"></script>
        <script src="{{asset('assets/js/jquery.scrollTo.min.js')}}"></script>

        <!-- Required datatable js -->
        <script src="{{asset("assets/plugins/datatables/jquery.dataTables.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/dataTables.bootstrap4.min.js")}}"></script>
        <!-- Buttons examples -->
        <script src="{{asset("assets/plugins/datatables/dataTables.buttons.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/buttons.bootstrap4.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/jszip.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/pdfmake.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/vfs_fonts.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/buttons.html5.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/buttons.print.min.js")}}"></script>

        <!-- Key Tables -->
        <script src="{{asset("assets/plugins/datatables/dataTables.keyTable.min.js")}}"></script>

        <!-- Responsive examples -->
        <script src="{{asset("assets/plugins/datatables/dataTables.responsive.min.js")}}"></script>
        <script src="{{asset("assets/plugins/datatables/responsive.bootstrap4.min.js")}}"></script>

        <!-- Selection table -->
        <script src="{{asset("assets/plugins/datatables/dataTables.select.min.js")}}"></script>

        <!-- App js -->
        <script src="{{asset("assets/js/jquery.core.js")}}"></script>
        <script src="{{asset("assets/js/jquery.app.js")}}"></script>

        <script type="text/javascript">
            $(document).ready(function () {

                // Default Datatable
                $('#datatable').DataTable();

                //Buttons examples
                var table = $('#datatable-buttons').DataTable({
                    lengthChange: false,
                    buttons: ['copy', 'excel', 'pdf']
                });

                // Key Tables

                $('#key-table').DataTable({
                    keys: true
                });

                // Responsive Datatable
                $('#responsive-datatable').DataTable();

                // Multi Selection Datatable
                $('#selection-datatable').DataTable({
                    select: {
                        style: 'multi'
                    }
                });

                table.buttons().container()
                    .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
            });

        </script>

        <!-- KNOB JS -->
        <!--[if IE]>
        <script type="text/javascript" src="assets/plugins/jquery-knob/excanvas.js"></script>
        <![endif]-->
        <script src="{{asset('assets/plugins/jquery-knob/jquery.knob.js')}}"></script>

        <!--Morris Chart-->
		<script src="{{asset('assets/plugins/morris/morris.min.js')}}"></script>
		<script src="{{asset('assets/plugins/raphael/raphael-min.js')}}"></script>

        <!-- Dashboard init -->
        <script src="{{asset('assets/pages/jquery.dashboard.js')}}"></script>

        <!-- App js -->
        <script src="{{asset('assets/js/jquery.core.js')}}"></script>
        <script src="{{asset('assets/js/jquery.app.js')}}"></script>
        
</header>
</body>
</html>