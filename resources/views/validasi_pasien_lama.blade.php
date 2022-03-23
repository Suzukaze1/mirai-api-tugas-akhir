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
                    <h3 class="header-title m-b-30">Detail Pasien</h3>
                    <a href="/list-pasien-lama" class="btn btn-small btn-primary">Kembali</a>
                    <br><br>
                    @foreach ($pasien as $p)
                    @foreach ($agama as $a)
                    @foreach ($pendidikan_terakhir as $pt)
                    @foreach ($kewarganegaraan_kode as $kk)
                    @foreach ($jenis_identitas_kode as $jik)
                    @foreach ($jenis_kelamin as $jk)
                    @foreach ($status_perkawinan as $sp)
                    @foreach ($kedudukan_keluarga as $kke)
                    @foreach ($golongan_darah as $gd)
                    @foreach ($provinsi as $pr)
                    @foreach ($kabupaten as $kb)
                    @foreach ($kecamatan as $kc)
                    @foreach ($penghasilan as $pg)

                    <h4>Biodata Pasien</h4>
                    <hr>
                    <br>
                        
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Lengkap Pasien</label>
                        <div class="col-10">
                            <input type="text" class="form-control " value="{{$p->nama}}" name="nama_lengkap" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Jenis Identitas</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$jik->nama}}" name="jenis_identitas" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Identitias</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->no_identitas}}" name="nomor_identitas"  readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Tempat Lahir</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->tempat_lahir}}" name="tempat_lahir" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Tanggal Lahir</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->tanggal_lahir}}" name="tanggal_lahir" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Anak Ke</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->anak_ke}}" name="anak_ke" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Kedudukan Keluarga</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$kke->nama}}" name="kedudukan_keluarga" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Golongan Darah</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$gd->nama}}" name="golongan_darah" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Agama</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$a->agama}}" name="agama" readonly="">
                        </div>
                    </div>

                    @if ($suku_kode == null)
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Suku</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="-" name="jenis_kelamin" readonly="">
                        </div>
                    </div>
                    @endif
                    @if (!$suku_kode == null)
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Suku</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{ $suku_kode->nama }}" name="suku" readonly="">
                        </div>
                    </div>    
                    @endif

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Jenis Kelamin</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$jk->nama}}" name="jenis_kelamin" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Telepon</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->no_telp}}" name="no_telp" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Alamat Lengkap</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->alamat}}" name="alamat" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Provinsi</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$pr->nama}}" name="provinsi" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Kabupaten</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$kb->nama}}" name="kabupaten" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Kecamatan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$kc->nama}}" name="kecamatan" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Kewarganegaraan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$kk->nama}}" name="kewarganegaraan" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Status Pernikahan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$sp->nama}}" name="status_perkawinan" readonly="">
                        </div>
                    </div>

                    <br>
                    <h4>Pekerjaan</h4>
                    <hr>
                    <br>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Pendidikan Terakhir</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$pt->nama}}" name="pendidikan_terakhir" readonly="">
                        </div>
                    </div>

                    @if ($jurusan == null)
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Jurusan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="-" name="jurusan" readonly="">
                        </div>
                    </div>
                    @endif
                    @if (!$jurusan == null)
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Jurusan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$jurusan->nama}}" name="jurusan" readonly="">
                        </div>
                    </div>
                    @endif

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Tempat Bekerja</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->nama_tempat_bekerja}}" name="nama_tempat_bekerja" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Alamat Tempat Bekerja</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->alamat_tempat_bekerja}}" name="alamat_tempat_bekerja" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Penghasilan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$pg->nama}}" name="penghasilan" readonly="">
                        </div>
                    </div>

                    <br>
                    <h4>Orang Tua</h4>
                    <hr>
                    <br>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Ayah</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->ayah_nama}}" name="ayah_nama" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Rekam Medik Ayah</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->no_rekam_medik_ayah}}" name="no_rekam_medik_ayah" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Ibu</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->ibu_nama}}" name="ibu_nama" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Rekam Medik Ibu</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$p->no_rekam_medik_ibu}}" name="no_rekam_medik_ibu" readonly="">
                        </div>
                    </div>

                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach
                    @endforeach

                    <br>
                    <h4>Penanggung</h4>
                    <hr>
                    <br>

                    @foreach ($penanggung as $pen)
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Penanggung</label>
                        <div class="col-10">
                            @if ($pen->nama_penanggung == 2)
                            <input type="text" class="form-control" value="BPJS" name="nama_penanggung" readonly="">
                            @endif
                            @if ($pen->nama_penanggung == 3)
                            <input type="text" class="form-control" value="KIS" name="nama_penanggung" readonly="">
                            @endif
                            @if ($pen->nama_penanggung == 4)
                            <input type="text" class="form-control" value="JAMKESDA" name="nama_penanggung" readonly="">
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Kartu Penanggung</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$pen->nomor_kartu_penanggung}}" name="nomor_kartu_penanggung" readonly="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nomor Kartu Penanggung</label>
                        <div class="col-10">
                            <img src="{{ $pen->foto_kartu_penanggung }}" alt="Card image cap" width="700" height="500">
                        </div>
                    </div>
                    
                    @endforeach

                    <br>
                    <h4>Foto</h4>
                    <hr>
                    <br>

                    @foreach ($foto_pasien as $fps)

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Foto Swa Pasien</label>
                        <div class="col-10">
                            <img src="{{ $fps->foto_swa_pasien }}" alt="Card image cap" width="500" height="800">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Foto Kartu Pasien</label>
                        <div class="col-10">
                            <img src="{{ $fps->foto_kartu_identitas_pasien }}" alt="Card image cap" width="700" height="500">
                        </div>
                    </div>

                    @endforeach

                    <br>
                    <h4>Akun</h4>
                    <hr>
                    <br>

                    @foreach ($akun as $akn)

                    <div class="form-group row">
                        <label class="col-2 col-form-label">Email</label>
                        <div class="col-10">
                            <input type="text" class="form-control" value="{{$akn->email}}" name="nomor_kartu_penanggung" readonly="">
                        </div>
                    </div>

                    <br>
                    <h4>Finishing</h4>
                    <hr>
                    <br>
                    
                    <form action="/list-pasien-lama/validasi/verifikasi" method="post" class="form-horizontal" role="form">
                        @csrf
                        <input type="hidden" name="id" value="{{ $akn->id }}">

                        <div class="form-group row">
                            <label class="col-2 col-form-label">Status Validasi</label>
                            <div class="col-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="id_status_validasi" value=1 checked> Berhasil Validasi
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="id_status_validasi" value=2 > Gagal Validasi
                                </div>  
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Alasan</label>
                            <div class="col-10">
                                <input type="text" class="form-control" name="alasan_berhasil_gagal" placeholder="Alasan Berhasil Atau gagal">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </form>     
                    @endforeach
                </div>    
            </div>
        </div>        
    </div>
</div>   
@endsection    