<!-- menghubungkan dengan view template -->
@extends('template')

<!-- isi bagian judul halaman -->
<!-- cara penulisan isi section yang pendek -->
@section('judul_halaman', 'Dashboard')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('konten')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4 class="m-t-0 header-title">Tabel List Pasien Yang Butuh Di Verifikasi</h4>
                                <hr>
                                @if(session('pesan'))
                                <div class="alert alert-success alert-dismisable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-check"></i> Success! </h4>
                                    {{session('pesan')}}.
                                </div>
                                @endif

                                @if(session('pesangagal'))
                                <div class="alert alert-danger alert-dismisable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="mdi mdi-block-helper"></i> Gagal! </h4>
                                    {{session('pesangagal')}}.
                                </div>
                            @endif 
                                <table id="datatable" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jenis Identitas</th>
                                        <th>Nomor Identitas</th>
                                        <th>Status Validasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>


                                    <tbody>
                                        <?php $no=1?>
                                    @foreach($pasien as $p)
                                    <tr>
                                        <td>{{$no++}}</td>
                                        <td>{{$p->nama}}</td>
                                        @if ($p->jenis_identitas_kode == 1)
                                            <td>KTP</td>
                                        @endif
                                        @if($p->jenis_identitas_kode == 2)
                                            <td>KIA</td>
                                        @endif
                                        @if($p->jenis_identitas_kode == 3)
                                            <td>Passport</td>
                                        @endif
                                        <td>{{$p->no_identitas}}</td>
                                        @if ($p->status_validasi == 0)
                                            <td style="color:green">Menunggu Validasi</td>
                                        @endif
                                        @if($p->status_validasi == 2)
                                            <td style="color:red">Validasi Ditolak</td>
                                        @endif
                                        <td>
                                            <a href="/list-pasien/validasi/{{$p->id}}" class="btn btn-sm btn-warning">Validasi</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- end row -->
                </div>    
            </div>
        </div>        
    </div>
</div>   
@endsection    