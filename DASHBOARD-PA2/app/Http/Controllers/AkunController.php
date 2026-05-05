<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    /**
     * Generate unique username from name
     */
    private function generateUsername($baseName)
    {
        // Convert to lowercase and remove spaces
        $baseUsername = strtolower(str_replace(' ', '', $baseName));
        
        // Check if username already exists
        if (!Akun::where('username', $baseUsername)->exists()) {
            return $baseUsername;
        }
        
        // If exists, add suffix (01, 02, 03, ...)
        $counter = 1;
        while (Akun::where('username', $baseUsername . str_pad($counter, 2, '0', STR_PAD_LEFT))->exists()) {
            $counter++;
        }
        
        return $baseUsername . str_pad($counter, 2, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $akun = Akun::all();
        return view('akun.index', compact('akun'));
    }

    public function create()
    {
        $guru = Guru::all();
        $siswa = Siswa::all();
        return view('akun.create', compact('guru', 'siswa'));
    }

    public function store(Request $request)
    {
        $role = $request->input('role');
        
        // Determine which name to use for username generation
        $name = null;
        if ($role === 'guru') {
            $guru = Guru::find($request->input('id_guru'));
            $name = $guru->nama_guru ?? null;
        } elseif ($role === 'orangtua') {
            $siswa = Siswa::find($request->input('nomor_induk_siswa'));
            $name = $siswa->nama_siswa ?? null;
        }
        
        // Generate unique username
        $username = $name ? $this->generateUsername($name) : null;
        
        $validated = $request->validate([
            'id_guru' => $role === 'guru' ? 'required|exists:guru,id_guru' : 'nullable|exists:guru,id_guru',
            'nomor_induk_siswa' => $role === 'orangtua' ? 'required|exists:siswa,nomor_induk_siswa' : 'nullable|exists:siswa,nomor_induk_siswa',
            'role' => 'required|in:guru,orangtua',
            'is_super_admin' => 'nullable|boolean',
        ], [
            'id_guru.required' => 'Guru wajib dipilih untuk akun dengan role guru',
            'nomor_induk_siswa.required' => 'Siswa wajib dipilih untuk akun dengan role orangtua',
        ]);

        // Set default password to 'password123'
        $validated['password'] = Hash::make('password123');
        $validated['is_super_admin'] = isset($validated['is_super_admin']) ? 1 : 0;
        $validated['username'] = $username;
        
        Akun::create($validated);
        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan dengan username: ' . $username);
    }

    public function show(Akun $akun)
    {
        return view('akun.show', compact('akun'));
    }

    public function edit(Akun $akun)
    {
        $guru = Guru::all();
        $siswa = Siswa::all();
        return view('akun.edit', compact('akun', 'guru', 'siswa'));
    }

    public function update(Request $request, Akun $akun)
    {
        $validated = $request->validate([
            'id_guru' => $request->input('role') === 'guru' ? 'required|exists:guru,id_guru' : 'nullable|exists:guru,id_guru',
            'nomor_induk_siswa' => $request->input('role') === 'orangtua' ? 'required|exists:siswa,nomor_induk_siswa' : 'nullable|exists:siswa,nomor_induk_siswa',
            'username' => 'required|string|max:50|unique:akun,username,' . $akun->id_akun . ',id_akun',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:guru,orangtua',
            'is_super_admin' => 'nullable|boolean',
        ], [
            'id_guru.required' => 'Guru wajib dipilih untuk akun dengan role guru',
            'nomor_induk_siswa.required' => 'Siswa wajib dipilih untuk akun dengan role orangtua',
            'username.unique' => 'Username sudah digunakan oleh akun lain',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->input('password'));
        } else {
            // Remove password from validated array if not provided
            unset($validated['password']);
        }

        $validated['is_super_admin'] = isset($validated['is_super_admin']) ? 1 : 0;
        $akun->update($validated);
        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui');
    }

    public function destroy(Akun $akun)
    {
        $akun->delete();
        return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus');
    }

    /**
     * Show form for bulk generate student accounts
     */
    public function bulkGenerateSiswaForm()
    {
        // Count students without accounts
        $siswaWithoutAccount = Siswa::whereDoesntHave('akun')->count();
        
        return view('akun.bulk-generate-siswa', compact('siswaWithoutAccount'));
    }

    /**
     * Bulk generate accounts for all students
     */
    public function bulkGenerateSiswaStore(Request $request)
    {
        // Find all students that don't have accounts yet
        $siswaList = Siswa::whereDoesntHave('akun')->get();
        
        if ($siswaList->isEmpty()) {
            return redirect()->route('akun.index')
                ->with('warning', 'Semua siswa sudah memiliki akun. Tidak ada siswa baru yang perlu di-generate akun.');
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            foreach ($siswaList as $siswa) {
                try {
                    // Generate username from student name
                    $username = $this->generateUsername($siswa->nama_siswa);
                    
                    // Create account for student
                    Akun::create([
                        'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
                        'username' => $username,
                        'password' => Hash::make('password123'),
                        'role' => 'orangtua',
                        'is_super_admin' => 0,
                    ]);
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Siswa {$siswa->nama_siswa}: {$e->getMessage()}";
                }
            }

            $message = "✓ Berhasil generate {$successCount} akun siswa";
            if ($errorCount > 0) {
                $message .= " (Gagal: {$errorCount})";
            }

            return redirect()->route('akun.index')
                ->with('success', $message)
                ->with('errors', !empty($errors) ? $errors : null);

        } catch (\Exception $e) {
            return redirect()->route('akun.index')
                ->with('error', 'Gagal generate akun: ' . $e->getMessage());
        }
    }
}
