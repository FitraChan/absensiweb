@extends('layout.main-admin')

@section('tittle-admin')
    Pengaturan
@endsection

@section('content-admin')

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">

        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Pengaturan Profile
            </h1>

            <small class="text-muted">
                Data profile perusahaan
            </small>
        </div>

        <a href="{{ route('profile.create') }}"
           class="btn btn-primary">
            <i class="fa fa-plus"></i>
            Tambah Profile
        </a>

    </div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-header bg-white">
            <strong>Daftar Profile Perusahaan</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Perusahaan</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Website</th>
                            <th>Alamat</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($data as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <strong>
                                    {{ $item->nama_perusahaan }}
                                </strong>
                            </td>

                            <td>
                                {{ $item->email ?? '-' }}
                            </td>

                            <td>
                                {{ $item->telepon ?? '-' }}
                            </td>

                            <td>
                                @if($item->website)
                                    <a href="{{ $item->website }}"
                                       target="_blank">
                                        {{ $item->website }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $item->alamat ?? '-' }}
                            </td>

                            <td>
                                {{ $item->lat ?? '-' }}
                            </td>

                            <td>
                                {{ $item->lang ?? '-' }}
                            </td>

                            <td>

                                <div class="d-flex gap-1">

                                    <a href="{{ route('profile.edit', $item->id) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Edit">

                                        <i class="fa fa-edit"></i>

                                    </a>


                                    <form action="{{ route('profile.destroy', $item->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus profile ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                title="Hapus">

                                            <i class="fa fa-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9"
                                class="text-center text-muted py-4">

                                <i class="fa fa-info-circle"></i>
                                Belum ada data profile.

                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection