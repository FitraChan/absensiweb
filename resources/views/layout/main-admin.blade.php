<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('tittle-admin')</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="{{asset('public/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">



    <link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css"/>


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />


    <style>
        body,table{
            color: #474646!important;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon ">
                    {{-- <i class="fas fa-laugh-wink"></i> --}}

                    <?php// print_r(asset('img/logo_bprs.jpg')); ?>
                    <img src="" width="50px">
                </div>
                {{-- <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div> --}}
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item @if (isset($cekNav) && $cekNav == 'dashboard') active @endif">
                <a class="nav-link" href="{{route('admin.index')}}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item @if (isset($cekNav) && $cekNav == 'setting') active @endif">
                <a class="nav-link" href="{{route('profile', 1)}}">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
            <li class="nav-item @if (isset($cekNav) && $cekNav == 'absen') active @endif">
                <a class="nav-link" href="{{route('absensi.index')}}">
                    <i class="fas fa-user-clock"></i>
                    <span>Absensi Karyawan</span>
                </a>
            </li>


            <li class="nav-item @if (isset($cekNav) && $cekNav == 'payroll') active @endif">
                <a class="nav-link" href="{{route('penggajian.index')}}">
                    <i class="fas fa-user-clock"></i>
                    <span>Payroll</span>
                </a>
            </li>


            <li class="nav-item @if (isset($cekNav) && $cekNav == 'kabsen') active @endif">
                <a class="nav-link" href="{{route('karywanAbsen.index')}}">
                    <i class="fas fa-user-clock"></i>
                    <span>Izin Karyawan</span>
                </a>
            </li>
            <li class="nav-item @if (isset($cekNav) && $cekNav == 'admin') active @endif">
                <a class="nav-link" href="{{route('adminKaryawan.index')}}">
                    <i class="fas fa-user-clock"></i>
                    <span>Data Admin</span>
                </a>
            </li>
            <li class="nav-item @if (isset($cekNav) && $cekNav == 'group') active @endif">
                <a class="nav-link" href="{{route('group.index')}}">
                    <i class="fas fa-users"></i>
                    <span>Group Karyawan</span>
                </a>
            </li>

            <li class="nav-item @if (isset($cekNav) && $cekNav == 'lembur') active @endif">
                <a class="nav-link" href="{{route('lembur.index')}}">
                    <i class="fas fa-users"></i>
                    <span>Lembur</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Data
            </div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fa fa-database"></i>
                    <span>Master Data</span>
                </a>
                <div id="collapseTwo" class="collapse @if (isset($cekNav2)) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'karyawan') active @endif" href="{{route('karyawan.index')}}"><i class="fas fa-database"></i> Data Karyawan</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'dapartement') active @endif" href="{{route('dapartement.index')}}"><i class="fas fa-database"></i> Data Dapartemen</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'jabatan') active @endif" href="{{route('jabatan.index')}}"><i class="fas fa-database"></i> Data Jabatan</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'jamKerja') active @endif" href="{{route('jamKerja.index')}}"><i class="fas fa-database"></i> Data Jam Kerja</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'gaji') active @endif" href="{{route('gaji.index')}}"><i class="fas fa-database"></i> Periode Gaji</a>

                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'setPayroll') active @endif" href="{{route('payrollSetting.index')}}"><i class="fas fa-database"></i> Setting Gaji</a>




                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'shifts') active @endif" href="{{route('jadwalKerja.index')}}"><i class="fas fa-database"></i> Shift Karyawan</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'grup') active @endif" href="{{url('groupKerja')}}"><i class="fas fa-database"></i> Group Shift</a>

                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'libur') active @endif" href="{{url('penentuanLibur')}}"><i class="fas fa-database"></i> Setting Libur</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'item-gaji') active @endif" href="{{url('item-gaji')}}"><i class="fas fa-database"></i> Item Gaji</a>
                        <a class="collapse-item @if (isset($cekNav2) && $cekNav2 == 'aturan-potongan') active @endif" href="{{url('aturan-potongan')}}"><i class="fas fa-database"></i> Aturan Potongan</a>


                    </div>

                </div>
            </li>
            <li class="nav-item">

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse3"
                    aria-expanded="true" aria-controls="collapse3">
                    <i class="fas fa-regular fa-folder"></i>
                    <span>Lainnya</span>
                </a>

                <div id="collapse3" class="collapse @if (isset($cekNav3)) show @endif" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item @if (isset($cekNav3) && $cekNav3 == 'pendidikan') active @endif" href="{{route('pendidikan.index')}}"><i class="fas fa-book"></i> Pendidikan</a>
                        <a class="collapse-item @if (isset($cekNav3) && $cekNav3 == 'agama') active @endif" href="{{route('agama.index')}}"><i class="fas fa-synagogue"></i> Agama</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                      <li class="nav-item dropdown no-arrow">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">
                          <div class="container">
                              <a class="navbar-brand" href="#">Inbox</a>

                              <div class="ml-auto d-flex align-items-center">


                                  <!-- Ikon Inbox -->
                                  <a href="{{ route('inbox.index') }}" class="position-relative text-dark mr-3">
                                    @if($unreadMessages ?? 0 > 0)
                                       <span class="badge badge-danger position-absolute" style="top: -5px; right: -10px;">
                                           {{ $unreadMessages ?? 0 }}
                                       </span>
                                   @endif

                                  </a>

                                  <!-- Toggle Navbar -->
                                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                                      <span class="navbar-toggler-icon"></span>
                                  </button>
                              </div>
                          </div>
                        </nav>

                      </li>

                        <div class="topbar-divider d-none d-sm-block"></div>



                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle"
                                    src="{{asset('public/img/undraw_profile.svg')}}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                {{-- <div class="dropdown-divider"></div> --}}
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <div class="px-3">
                    @include('layout.response')
                </div>
                <!-- Begin Page Content -->
                @yield('content-admin')
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright my-auto">
                       
                        <span class="float-right">Redeveloped by MBC Consulting</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{route('logout')}}">Logout</a>
                </div>
            </div>
        </div>
    </div>




    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    {{-- <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('js/sb-admin-2.min.js')}}"></script>

    <!-- Page level plugins -->
    {{-- <script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script> --}}

    <!-- Page level custom scripts -->
    {{-- <script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
    <script src="{{asset('js/demo/chart-pie-demo.js')}}"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>



    <script>
        // $(document).ready(function () {
        //     $('select').selectize({
        //         sortField: 'text'
        //     });
        // });
      </script>

    <script type="application/javascript">
        function confirm_delete(target, msg) {
            Swal.fire({
                    title: 'Yakin Menghapus ??',
                    text: "Pastikan Yang Kamu Pilih Benar",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: target,
                            data:{
                                _token: '{{csrf_token()}}'
                            },
                            success: function(url){
                                console.log(url);
                                Swal.fire(
                                    'Terhapus!',
                                    'Status Berhasil Di Hapus !!',
                                    'success'
                                ),
                                table.draw();
                            }
                        });

                    }
                })
        }
    </script>

    <script type="application/javascript">
        function confirm_update(target, msg) {
            Swal.fire({
                    title: 'Yakin Mengubah ??',
                    text: "Pastikan Yang Kamu Pilih Benar",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "PUT",
                            url: target,
                            data:{
                                _token: '{{csrf_token()}}'
                            },
                            success: function(url){
                                console.log(url);
                                Swal.fire(
                                    'Terhapus!',
                                    'Status Berhasil Di Hapus !!',
                                    'success'
                                ),
                                table.draw();
                            }
                        });

                    }
                })
        }
    </script>



</body>

</html>
