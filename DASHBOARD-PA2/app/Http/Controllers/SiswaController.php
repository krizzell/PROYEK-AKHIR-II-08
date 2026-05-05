<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Filter berdasarkan NIS
        if ($request->filled('nis')) {
            $query->where('nomor_induk_siswa', 'like', '%' . $request->nis . '%');
        }

        // Filter berdasarkan Nama Siswa
        if ($request->filled('nama')) {
            $query->where('nama_siswa', 'like', '%' . $request->nama . '%');
        }

        // Filter berdasarkan Kelas
        if ($request->filled('kelas')) {
            $query->where('id_kelas', $request->kelas);
        }

        // Filter berdasarkan Jenis Kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $siswa = $query->get();
        $kelas = Kelas::all();

        return view('siswa.index', compact('siswa', 'kelas'));
    }

    public function create()
    {
        // Tampilkan SEMUA kelas yang tersedia
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|numeric|unique:siswa,nomor_induk_siswa',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'nama_siswa' => 'required|string|max:150',
            'nama_orgtua' => 'required|string|max:150',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
        ]);

        Siswa::create($validated);
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function show($nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        return view('siswa.show', compact('siswa'));
    }

    public function edit($nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        // Tampilkan SEMUA kelas yang tersedia
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|numeric|unique:siswa,nomor_induk_siswa,' . $siswa->nomor_induk_siswa . ',nomor_induk_siswa',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'nama_siswa' => 'required|string|max:150',
            'nama_orgtua' => 'required|string|max:150',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
        ]);

        $siswa->update($validated);
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy($nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus');
    }

    public function importForm()
    {
        $kelas = Kelas::all();
        return view('siswa.import', compact('kelas'));
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv',
        ], [
            'file.required' => 'File wajib diunggah',
            'file.mimes' => 'File harus dalam format CSV (.csv)',
        ]);

        $file = $request->file('file');
        $errors = [];
        $successCount = 0;

        try {
            // Parse CSV file
            $handle = fopen($file->getRealPath(), 'r');
            $headers = [];
            $firstRow = true;
            $rowNumber = 1;
            
            while (($row = fgetcsv($handle)) !== false) {
                if ($firstRow) {
                    // Remove BOM from first cell if present
                    if (!empty($row[0])) {
                        $row[0] = preg_replace("/^\xEF\xBB\xBF/", '', $row[0]);
                    }
                    
                    // Get headers - clean and normalize
                    $headers = [];
                    foreach ($row as $header) {
                        $headers[] = strtolower(trim($header));
                    }
                    $firstRow = false;
                    
                    // Validate headers with flexible matching
                    $nisIndex = null;
                    $namaIndex = null;
                    $kelasIndex = null;
                    $orangtuaIndex = null;
                    $jenisIndex = null;
                    $alamatIndex = null;
                    
                    foreach ($headers as $idx => $header) {
                        // Skip empty headers
                        if (empty($header)) continue;
                        
                        if (in_array($header, ['nisn', 'nis', 'no. induk siswa', 'nomor induk siswa'])) $nisIndex = $idx;
                        if (in_array($header, ['nama siswa', 'nama_siswa', 'nama'])) $namaIndex = $idx;
                        if (in_array($header, ['kelas', 'id_kelas', 'class'])) $kelasIndex = $idx;
                        if (in_array($header, ['orang tua', 'orang_tua', 'nama orang tua', 'parent'])) $orangtuaIndex = $idx;
                        if (in_array($header, ['jenis kelamin', 'jenis_kelamin', 'gender'])) $jenisIndex = $idx;
                        if (in_array($header, ['alamat', 'address', 'alamat_tinggal'])) $alamatIndex = $idx;
                    }

                    if ($nisIndex === null || $namaIndex === null || $kelasIndex === null || $orangtuaIndex === null || $jenisIndex === null) {
                        fclose($handle);
                        return redirect()->route('siswa.index')
                            ->with('error', 'Format CSV tidak sesuai. Kolom yang diperlukan: NISN, Nama Siswa, Kelas, Orang Tua, Jenis Kelamin');
                    }
                    
                    continue;
                }
                
                $rowNumber++;

                // Skip empty rows
                if (empty($row[$nisIndex])) {
                    continue;
                }

                try {
                    $nis = (string) trim($row[$nisIndex]);
                    $nama = trim($row[$namaIndex] ?? '');
                    $kelasNama = trim($row[$kelasIndex] ?? '');
                    $orangtua = trim($row[$orangtuaIndex] ?? '');
                    $jenis = strtoupper(substr(trim($row[$jenisIndex] ?? ''), 0, 1));
                    $alamat = trim($row[$alamatIndex] ?? '-');
                    if (empty($alamat)) $alamat = '-';

                    // Validasi
                    if (empty($nama)) {
                        $errors[] = "Baris $rowNumber: Nama siswa kosong";
                        continue;
                    }
                    if (empty($orangtua)) {
                        $errors[] = "Baris $rowNumber: Nama orang tua kosong";
                        continue;
                    }
                    if (!in_array($jenis, ['L', 'P'])) {
                        $errors[] = "Baris $rowNumber: Jenis kelamin harus 'L' atau 'P'";
                        continue;
                    }

                    // Find kelas by name
                    $kelas = Kelas::where('nama_kelas', 'like', "%$kelasNama%")->first();
                    if (!$kelas) {
                        $errors[] = "Baris $rowNumber: Kelas '$kelasNama' tidak ditemukan";
                        continue;
                    }

                    // Check if siswa already exists
                    $siswaExists = Siswa::where('nomor_induk_siswa', $nis)->exists();
                    
                    if (!$siswaExists) {
                        Siswa::create([
                            'nomor_induk_siswa' => $nis,
                            'id_kelas' => $kelas->id_kelas,
                            'nama_siswa' => $nama,
                            'nama_orgtua' => $orangtua,
                            'jenis_kelamin' => $jenis,
                            'tgl_lahir' => now()->toDateString(),
                            'alamat' => $alamat,
                        ]);
                        $successCount++;
                    } else {
                        // Update if exists
                        Siswa::where('nomor_induk_siswa', $nis)->update([
                            'id_kelas' => $kelas->id_kelas,
                            'nama_siswa' => $nama,
                            'nama_orgtua' => $orangtua,
                            'jenis_kelamin' => $jenis,
                            'alamat' => $alamat,
                        ]);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                }
            }
            
            fclose($handle);

            $message = "Import berhasil! $successCount data siswa telah ditambahkan/diperbarui";
            if (!empty($errors)) {
                $message .= ". Tetapi ada " . count($errors) . " baris yang gagal.";
                return redirect()->route('siswa.index')
                    ->with('warning', $message)
                    ->with('errors', $errors);
            }

            return redirect()->route('siswa.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('siswa.index')
                ->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }
}
