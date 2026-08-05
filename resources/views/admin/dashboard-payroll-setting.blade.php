@extends('layout.main-admin')
@section('tittle-admin')
  Setting Payroll
@endsection
@section('content-admin')

{{-- modal --}}




<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Tambah Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{route('payrollSetting.store')}}">

        @csrf
        <div class="modal-body">
          <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inputEmail4">Dapartement</label>
            <select name="departemen_id" class="form-control">
              @foreach ($departement as $dpt)
                <option value="{{$dpt->id}}">{{$dpt->nama_departement}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="inputEmail4">Item Gaji</label>
            <select name="item_gaji_id" class="form-control">
              @foreach ($itemGaji as $dpt)
                <option value="{{$dpt->id}}">{{$dpt->nama_item_gaji}}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group col-md-6">
            <label for="inputEmail4">Nominal</label>
            <input type="number" name="nominal" class="form-control">
          </div>


        </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Tambah</button>
    </div>
  </form>
      </div>
    </div>
  </div>

@foreach ($data->get() as $lks)
<!-- Modal -->
<div class="modal fade" id="data-{{$lks->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="data-{{$lks->id}}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="data-{{$lks->id}}Label"> Setting Payroll</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('payrollSetting.update', $lks->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail4">Dapartement</label>
              <select name="departemen_id" class="form-control">
                @foreach ($departement as $dpt)
                  <option {{$lks->departemen_id == $dpt->id?'selected':''}} value="{{$dpt->id}}">{{$dpt->nama_departement}}</option>
                @endforeach
              </select>


            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail4">Item Gaji</label>
              <select name="item_gaji_id" class="form-control">
                @foreach ($itemGaji as $dpt)
                  <option {{$lks->item_gaji_id == $dpt->id?'selected':''}} value="{{$dpt->id}}">{{$dpt->nama_item_gaji}}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group col-md-6">
              <label for="inputEmail4">Nominal</label>
              <input type="number" value="{{$lks->nominal}}" name="nominal" class="form-control">
              <input type="hidden" value="{{$lks->id}}" name="id" class="form-control">


            </div>


          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Ubah</button>
      </div>
    </form>
    </div>
  </div>
</div>
@endforeach


<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Setting Payroll Karyawan</h1>

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
      Tambah
    </button>
  </div>

  <div class="row mb-5">
    <div class="col-sm-5">


        <select name="departement_id" class="form-control" id="departement_id">
          @foreach ($departement as $dpt)
            <option value="{{$dpt->id}}">{{$dpt->nama_departement}}</option>
          @endforeach
        </select>

    </div>

  </div>



    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Departement</th>
                  <th>Item Gaji</th>
                  <th>Nominal</th>
                  <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama Departement</th>
                  <th>Item Gaji</th>
                  <th>Nominal</th>
                  <th>Action</th>
                </tr>
            </tfoot>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<style scoped>
  .hover-left i{
    transition: 0.5s;
  }
  .hover-left:hover > i{
    padding-right: 15px;
    transition: 0.5s;
  }

</style>

<script>
	var table = $('#dataTable').DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth:false,
		ajax: {
			url: "{{ route('payrollSetting.index') }}",
      data:function(d){
        d.departement_id = $('#departement_id').val()
      }
		},
		columns: [
				{data: 'DT_RowIndex', name: 'DT_Row_Index', orderable: false, searchable: false},
				{data: 'departemen_id', name: 'departemen_id'},
				{data: 'item_gaji_id', name: 'item_gaji_id'},
				{data: 'nominal', name: 'nominal'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
		  ],
		});

    $(function(){
      $("#departement_id").change(function(){
          table.draw();
      });

    });

</script>

@endsection
