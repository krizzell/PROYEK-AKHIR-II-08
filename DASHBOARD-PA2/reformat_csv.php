<?php
$inputFile = 'd:\\Semester 4\\Proyek Tahun II\\Week 14\\Praktikum\\data siswa FORMATTED.csv';
$outputFile = 'd:\\Semester 4\\Proyek Tahun II\\Week 14\\Praktikum\\data siswa siap import.csv';

if (file_exists($outputFile)) {
    unlink($outputFile);
}

$handle = fopen($inputFile, 'r');
if (!$handle) {
    die("Gagal membuka file input");
}

$output = fopen($outputFile, 'w');
if (!$output) {
    die("Gagal membuat file output");
}

// Write new header - tanpa kolom kosong
fputcsv($output, ['Nama Siswa', 'NISN', 'Orang Tua', 'Kelas', 'Jenis Kelamin', 'Alamat']);

$rowCount = 0;
while (($row = fgetcsv($handle)) !== false) {
    $rowCount++;
    // Skip header row
    if ($rowCount <= 1) continue;
    
    // Map columns dengan benar - abaikan kolom No dan kolom kosong (position 6)
    // Position: 0=No, 1=Nama, 2=NISN, 3=Orang Tua, 4=Kelas, 5=Jenis Kelamin, 6=[kosong], 7=Alamat
    $newRow = [
        $row[1] ?? '', // Nama Siswa
        $row[2] ?? '', // NISN
        $row[3] ?? '', // Orang Tua
        $row[4] ?? '', // Kelas
        $row[5] ?? '', // Jenis Kelamin
        $row[7] ?? '' // Alamat (skip position 6 yang kosong)
    ];
    
    fputcsv($output, $newRow);
}

fclose($handle);
fclose($output);
echo "File CSV berhasil diformat untuk import!\n";
echo "Lokasi: d:\\Semester 4\\Proyek Tahun II\\Week 14\\Praktikum\\data siswa siap import.csv\n";
?>
