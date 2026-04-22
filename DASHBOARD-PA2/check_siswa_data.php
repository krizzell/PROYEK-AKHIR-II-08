<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$siswa = DB::table('siswa')->limit(10)->get();
echo "=== Data Siswa ===\n";
foreach($siswa as $s) {
    echo "NIS: " . ($s->nomor_induk_siswa ?? 'NULL') . 
         " | Nama: " . ($s->nama_siswa ?? 'NULL') . 
         " | Orangtua: " . ($s->nama_orgtua ?? 'NULL') . 
         " | Kelas: " . ($s->id_kelas ?? 'NULL') . "\n";
}

echo "\n=== Siswa dengan NIS kosong/NULL ===\n";
$kosong = DB::table('siswa')->where('nomor_induk_siswa', '')->orWhereNull('nomor_induk_siswa')->get();
echo "Total: " . count($kosong) . "\n";
foreach($kosong as $s) {
    echo "Nama: " . ($s->nama_siswa ?? 'NULL') . " | Orangtua: " . ($s->nama_orgtua ?? 'NULL') . "\n";
}
?>
