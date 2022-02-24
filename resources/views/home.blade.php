<!-- menghubungkan dengan view template -->
@extends('template')

<!-- isi bagian judul halaman -->
<!-- cara penulisan isi section yang pendek -->
@section('judul_halaman', 'MIRAI PASIEN')

<!-- isi bagian konten -->
<!-- cara penulisan isi section yang panjang -->
@section('konten')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <h1 class="display-4">Selamat Datang, Alvin</h1> 
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box">

                        </div>
                    </div><!-- end col -->  
                </div>    
            </div>
        </div>        
    </div>
</div>   
@endsection    