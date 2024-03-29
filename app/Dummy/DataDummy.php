<?php

namespace App\Dummy;

use App\Helpers\ResponseFormatter;
use Carbon\Carbon;

class DataDummy
{
    public static function dummyPoli()
    {
        $dumPoli = [
        ["id" => 1,
        "nama" => "Anak",
        "foto" => "/foto_poli/anak.png",],
        ["id" => 2,
        "nama" => "Bedah",
        "foto" => "/foto_poli/poli_bedah.png",]];

        return $dumPoli;
    }

    public static function dummyPilihPoli($id_poli)
    {
        $dumPoliList = [
        ["id" => 1,
        "nama" => "Anak",
        "foto" => "/foto_poli/anak.png",],
        ["id" => 2,
        "nama" => "Bedah",
        "foto" => "/foto_poli/poli_bedah.png",]];

        $dumPoli = array();
        foreach( $dumPoliList as $item ){
            if ( is_array( $item ) && isset( $item['id'] )){
                if ( $item['id'] == $id_poli ){ // or other string comparison
                    $dumPoli[] = $item;
                }
            }
        }
        return $dumPoli;
    }

    public static function dummyDokter($id_poli)
    {
        $dumDokter = [
            ["id" => 1,
            "nama" => "RIZA YEFRI",
            "id_poli" => "1",
            "gelar" => "SpA",
            "foto" => "/foto_dokter/riza_yefri.png",
            "jadwal" => [["hari" => "Senin", "waktu" => "09:00 - 14:00"], ["hari" => "Rabu", "waktu" => "08:00 - 12:00"]],
            "pendidikan_terakhir" => ["sarjana" => "FK UNRI KEDOKTERAN UNRI", "master" => "FK UI"],
            "tentang_dokter" => null],
            ["id" => 2,
            "nama" => "Dokter 2",
            "id_poli" => "2",
            "gelar" => "SpB",
            "foto" => "/foto_dokter/riza_yefri.png",
            "jadwal" => [["hari" => "Rabu", "waktu" => "09:00 - 14:00"], ["hari" => "Kamis", "waktu" => "08:00 - 12:00"]],
            "pendidikan_terakhir" => ["sarjana" => "FK UNRI KEDOKTERAN UNRI", "master" => "FK UI"],
            "tentang_dokter" => null],
            ["id" => 3,
            "nama" => "Dokter 3",
            "id_poli" => "1",
            "gelar" => "SpA",
            "foto" => "/foto_dokter/riza_yefri.png",
            "jadwal" => [["hari" => "Selasa", "waktu" => "09:00 - 14:00"], ["hari" => "Jumat", "waktu" => "08:00 - 11:00"]],
            "pendidikan_terakhir" => ["sarjana" => "FK UNRI KEDOKTERAN UNRI", "master" => "FK UI"],
            "tentang_dokter" => null]
        ];

        $list_dokter = array();
        foreach( $dumDokter as $item ){
            if ( is_array( $item ) && isset( $item['id_poli'] )){
                if ( $item['id_poli'] == $id_poli ){ // or other string comparison
                    $list_dokter[] = $item;
                }
            }
        }
        
        return $list_dokter;
    }

    public static function dummyListKamar()
    {
        $dumListKamar = [
            ["id" => 1,
            "nama_kelas" => "Kelas III",
            "foto" => "/foto_kelas/kelas-iii.png"],
            ["id" => 2,
            "nama_kelas" => "Kelas II",
            "foto" => "/foto_kelas/kelas-ii.png",],
            ["id" => 3,
            "nama_kelas" => "Kelas I",
            "foto" => "/foto_kelas/kelas-i.png",],
            ["id" => 4,
            "nama_kelas" => "VIP",
            "foto" => "/foto_kelas/vip.png",]];

        return $dumListKamar;
    }

    public static function dummyDetailKamar($id_kamar)
    {
        $dumDetailKamar = [
            [
                "id" => 1,
                "id_kamar" => "1",
                "nama_kamar" => "Ruang Anggrek 1",
                "total" => "10",
                "tersedia" => "4",
                "terisi" => "6"
            ],
            [
                "id" => 2,
                "id_kamar" => "1",
                "nama_kamar" => "Ruang Anggrek 2",
                "total" => "10",
                "tersedia" => "7",
                "terisi" => "3"
            ],
            [
                "id" => 3,
                "id_kamar" => "2",
                "nama_kamar" => "Ruang Anggrek 3",
                "total" => "15",
                "tersedia" => "8",
                "terisi" => "7"
            ],
            [
                "id" => 4,
                "id_kamar" => "4",
                "nama_kamar" => "Ruang VIP 1",
                "total" => "25",
                "tersedia" => "10",
                "terisi" => "15"
            ],
        ];

        $list_kamar = array();
        foreach( $dumDetailKamar as $item ){
            if ( is_array( $item ) && isset( $item['id_kamar'] )){
                if ( $item['id_kamar'] == $id_kamar ){ // or other string comparison
                    $list_kamar[] = $item;
                }
            }
        }
        return $list_kamar;
    }

    public static function dummyBantuan()
    {
        $dumBantuan = [
            [
                "id" => 1,
                "nama_bantuan" => "Kenapa Saya Tidak Bisa Login?",
                "detail_bantuan" => "Sebelum Login pastikan akun yang akan di loginkan sudah terdaftar pada aplikasi mirai pasien, jika sudah mendaftar tetapi juga tidak bisa login silahkan hubungi ke yang bersangkutan."
            ],
            [
                "id" => 2,
                "nama_bantuan" => "Kenapa Saya Tidak Menerima Kode OTP?",
                "detail_bantuan" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat"
            ],
            [
                "id" => 3,
                "nama_bantuan" => "Kenapa Email Sudah Digunakan Saat Melakukan Pendaftaran Pasien",
                "detail_bantuan" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat"
            ],
        ];

        return $dumBantuan;
    }

    public static function dummyDaftarAntrian()
    {
        $dumDaftarAtrian = [
            [
                "id" => 1,
                "nama_daftar_antrian" => "Pendaftaran",
                "foto" => "/foto_daftar_antrian/pendaftaran.png"
            ],
            [
                "id" => 2,
                "nama_daftar_antrian" => "Poliklinik",
                "foto" => "/foto_daftar_antrian/poliklinik.png"
            ],
            [
                "id" => 3,
                "nama_daftar_antrian" => "Apotek",
                "foto" => "/foto_daftar_antrian/apotek.png"
            ],
        ];

        return $dumDaftarAtrian;
    }

    public static function dummyDetailDaftarAntrianPendaftaran()
    {
        $dumDetailDaftarAntrianPendaftaran = [
            [
                "id" => 1,
                "id_daftar_antrian" => "1",
                "nama" => "Loket 1",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "32"
            ],
            [
                "id" => 2,
                "id_daftar_antrian" => "1",
                "nama" => "Loket 2",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "33"
            ],
            [
                "id" => 3,
                "id_daftar_antrian" => "1",
                "nama" => "Loket 3",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "34"
            ],
        ];

        return $dumDetailDaftarAntrianPendaftaran;
    }

    public static function dummyDetailDaftarAntrianPoliklinik($id_poli)
    {
        $dumDetailDaftarAntrianPoliklinik = [
            [
                "id" => 1,
                "id_daftar_antrian" => "2",
                "id_poli" => "1",
                "nama" => "Poliklinik Anak",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "32"
            ],
            [
                "id" => 2,
                "id_daftar_antrian" => "2",
                "id_poli" => "2",
                "nama" => "Poliklinik Bedah Plastik",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "33"
            ],
        ];

        $list_poliklinik = array();
        foreach( $dumDetailDaftarAntrianPoliklinik as $item ){
            if ( is_array( $item ) && isset( $item['id_poli'] )){
                if ( $item['id_poli'] == $id_poli ){ // or other string comparison
                    $list_poliklinik['id'] = $item['id'];
                    $list_poliklinik['id_daftar_antrian'] = $item['id_daftar_antrian'];
                    $list_poliklinik['nama'] = $item['nama'];
                    $list_poliklinik['jumlah_antrian'] = $item['jumlah_antrian'];
                    $list_poliklinik['antrian_berjalan'] = $item['antrian_berjalan'];
                }
            }
        }

        return $list_poliklinik;
    }

    public static function dummyDetailDaftarAntrianApotek()
    {
        $dumDetailDaftarAntrianApotek = [
            [
                "id" => 1,
                "id_daftar_antrian" => "3",
                "nama" => "Loket 1",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "19"
            ],
            [
                "id" => 2,
                "id_daftar_antrian" => "3",
                "nama" => "Loket 2",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "20"
            ],
            [
                "id" => 3,
                "id_daftar_antrian" => "3",
                "nama" => "Loket 3",
                "jumlah_antrian" => "110",
                "antrian_berjalan" => "21"
            ],
        ];

        return $dumDetailDaftarAntrianApotek;
    }

    public static function dummyHari()
    {
        $H_1 = Carbon::now()->addDay(1)->locale('id');
        $H_2 = Carbon::now()->addDay(2)->locale('id');
        $H_1->settings(['formatFunction' => 'translatedFormat']);
        $H_2->settings(['formatFunction' => 'translatedFormat']);
        $id_1 = Carbon::now()->addDay(1)->format('Y-m-d');
        $id_2 = Carbon::now()->addDay(2)->format('Y-m-d');
        $hari_1 = $H_1->isoFormat('dddd, D MMMM Y');
        $hari_2 = $H_2->isoFormat('dddd, D MMMM Y');
        $dumHari = [
            [
                "id_tanggal" => $id_1,
                "label_hari" =>$hari_1
            ],
            [
                "id_tanggal" => $id_2,
                "label_hari" =>$hari_2
            ],
        ];

        return $dumHari;
    }

    public static function dummyDebitur()
    {
        $dumDebitur = [
            [
                "jenis_debitur" => 1,
                "nama_debitur" => "Umum"
            ]
        ];

        return $dumDebitur;
    }

    public static function dummyUserBPJS($nomor_bpjs)
    {
        $dumBPJS = [
            ['nomor_rekam_medis' => '031391931',
            'nama_pasien' => 'Naufal Lawrence',
            'nomor_bpjs' => '000007',
            'status_rujukan' => '1',
            'status_kontrol' => '0',
            'rujukan' => [
                            (
                                [
                                'nomor_rujukan' => '989898988874232',
                                'id_poli' => '1',
                                'nama_poli' => 'poli bedah onkologi'
                                ]
                            ),
                            (
                                [
                                'nomor_rujukan' => '989898988874232',
                                'id_poli' => '1',
                                'nama_poli' => 'poli orthopedi & traumalogi'
                                ]
                            )
                        ],
            'kontrol' => []
        ],
            ['nomor_rekam_medis' => '00000031',
            'nama_pasien' => 'Klee',
            'nomor_bpjs' => '9999',
            'status_rujukan' => '1',
            'status_kontrol' => '0',
            'rujukan' => [
                            (
                                [
                                'nomor_rujukan' => '989898988874232',
                                'id_poli' => '1',
                                'nama_poli' => 'Anak'
                                ]
                            ),
                        ],
            'kontrol' => [],
            ],
            ['nomor_rekam_medis' => '00000032',
            'nama_pasien' => 'Arif',
            'nomor_bpjs' => '12323',
            'status_rujukan' => '0',
            'status_kontrol' => '1',
            'rujukan' => [],
            'kontrol' => [
                (
                    [
                    'nomor_rujukan' => '989898988874232',
                    'id_poli' => '1',
                    'nama_poli' => 'Anak'
                    ]
                ),
            ],
        ],
            ['nomor_rekam_medis' => '00000029',
            'nama_pasien' => 'lawachurl',
            'nomor_bpjs' => '11964949',
            'status_rujukan' => '1',
            'status_kontrol' => '1',
            'rujukan' => [
                (
                    [
                    'nomor_rujukan' => '989898988874232',
                    'id_poli' => '1',
                    'nama_poli' => 'Anak'
                    ]
                ),
            ],
            'kontrol' => [
                (
                    [
                    'nomor_rujukan' => '989898988874232',
                    'id_poli' => '1',
                    'nama_poli' => 'Anak'
                    ]
                ),
            ],
        ],
        ['nomor_rekam_medis' => '00000021',
            'nama_pasien' => 'Alvinmd',
            'nomor_bpjs' => '7319800976',
            'status_rujukan' => '1',
            'status_kontrol' => '0',
            'rujukan' => [
                (
                    [
                    'nomor_rujukan' => '546473593287',
                    'id_poli' => '2',
                    'nama_poli' => 'Bedah'
                    ]
                ),
            ],
            'kontrol' => [],
            ]
        ];

        $rujukan = array();
        foreach( $dumBPJS as $item ){
            if ( is_array( $item ) && isset( $item['nomor_bpjs'] )){
                if ( $item['nomor_bpjs'] == $nomor_bpjs){ // or other string comparison
                    $rujukan['nomor_rekam_medis'] = $item['nomor_rekam_medis'];
                    $rujukan['nama_pasien'] = $item['nama_pasien'];
                    $rujukan['nomor_bpjs'] = $item['nomor_bpjs'];
                    $rujukan['status_rujukan'] = $item['status_rujukan'];
                    $rujukan['status_kontrol'] = $item['status_kontrol'];
                    $rujukan['rujukan'] = $item['rujukan'];
                    $rujukan['kontrol'] = $item['kontrol'];
                }
            }
        }

        if(count($rujukan) == 0){
            return ResponseFormatter::error_not_found("Belum ada rujukan", null);
        }else{
            return ResponseFormatter::success_ok("Data rujukan ditemukan", $rujukan);
        }
        
    }
}