@extends('layout.main-admin')
@section('tittle-admin')
  Dashboard | Admin
@endsection
@section('content-admin')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{count($data)}}</div>
                            <div class="text-xl font-weight-bold text-primary text-uppercase mb-4">
                                Pengguna</div>
                            <div class="link-icon-right">
                              <a href="{{route('karyawan.index')}}" class="link-icon-right">Selengkapnya </a>
                              <i class="fas fa-arrow-circle-right text-primary"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{count($dapartement->get())}}</div>
                            <div class="text-xl font-weight-bold text-success text-uppercase mb-4">
                                Dapartemen</div>
                            <div class="link-icon-right">
                                <a href="{{route('dapartement.index')}}" class="link-icon-right">Selengkapnya </a>
                                <i class="fas fa-arrow-circle-right text-primary"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{count($jabatan->get())}}</div>
                            <div class="text-xl font-weight-bold text-warning text-uppercase mb-4">
                                Jabatan</div>
                            <div class="link-icon-right">
                                <a href="{{route('jabatan.index')}}" class="link-icon-right">Selengkapnya </a>
                                <i class="fas fa-arrow-circle-right text-primary"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>

 <style scoped>
   .link-icon-right:hover{
     margin-right: 15px;
     transition: 0.5s;
   }   
   .link-icon-right{
     transition: 0.5s;
   }
 </style>
@endsection