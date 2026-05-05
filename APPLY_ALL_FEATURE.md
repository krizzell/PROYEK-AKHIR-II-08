# Apply All Tagihan Feature - Implementation Guide

## Overview
"Apply All" adalah fitur untuk membuat tagihan (billing) secara massal untuk beberapa siswa sekaligus, tanpa perlu input manual satu per satu. Fitur ini mengimplementasikan role-based access control sesuai permintaan user.

## Role-Based Access Control

### 1. **Guru Biasa** (`is_super_admin = false`)
- ✅ Dapat membuat tagihan untuk **siswa di kelasnya saja**
- ❌ TIDAK dapat membuat tagihan untuk siswa di kelas lain
- Saat memilih "Semua Siswa" → hanya akan membuat tagihan untuk kelasnya

### 2. **Kepala Sekolah** (`is_super_admin = true`)
- ✅ Dapat membuat tagihan untuk **SEMUA siswa** di sekolah
- ✅ Dapat memilih untuk membuat tagihan **per kelas tertentu**
- ✅ Fleksibilitas penuh dalam bulk operations

## User Interface Changes

### Index Page (`resources/views/tagihan/index.blade.php`)
- **Added**: "Apply All" button dengan warna cyan (#06B6D4) untuk membedakan dari tombol "Buat Tagihan" biasa
- **Positioning**: Di sebelah kiri tombol "Buat Tagihan" di page header
- **Icon**: Lightning bolt (⚡) untuk menunjukkan bulk action

```
┌─────────────────────────────────────────┐
│ Data Tagihan          [Apply All] [Buat Tagihan] │
└─────────────────────────────────────────┘
```

## Feature Flow

### Step 1: Click "Apply All"
User diklik tombol Apply All di halaman Data Tagihan

### Step 2: Select Options
Form menampilkan opsi:
- **Target Pembuat Tagihan**: Semua Siswa / Per Kelas
- **Pilih Kelas** (conditional): Muncul hanya jika memilih "Per Kelas"
- **Jumlah Tagihan**: Input numeric (Rp)
- **Periode Tagihan**: Text input (default: "SPP Bulan {Month} {Year}")

### Step 3: Live Preview
Sebelum submit, user melihat preview:
- Jumlah siswa yang akan di-apply
- Target (semua siswa / nama kelas)
- Warning: "Duplikat tagihan dengan periode yang sama akan dilewati"

### Step 4: Apply
Sistem akan:
1. Cek duplikat tagihan per siswa dengan periode yang sama
2. Membuat tagihan hanya untuk siswa yang belum memiliki periode tersebut
3. Set `status` dan `payment_status` ke "belum_bayar"
4. Menampilkan hasil: "Tagihan berhasil dibuat untuk X siswa (Y siswa sudah memiliki tagihan untuk periode ini)"

## Controller Methods

### 1. `bulkCreate()` - Display Form
- **Route**: GET `/tagihan/bulk-create`
- **Authorization**: 
  - Guru: Only sees kelas yang diajar
  - Kepala Sekolah: Sees all kelas
- **Returns**: Form view dengan opsi kelas

### 2. `bulkCreateStore()` - Process Bulk Creation
- **Route**: POST `/tagihan/bulk-store`
- **Validation**:
  ```php
  'tipe_target' => 'required|in:semua_siswa,per_kelas'
  'id_kelas' => 'required_if:tipe_target,per_kelas'
  'jumlah_tagihan' => 'required|numeric|min:1'
  'periode' => 'required|string|max:20'
  ```
- **Authorization Check**:
  ```
  if (Guru && tipe_target = per_kelas)
    → Cek apakah kelas termasuk kelas guru
    → If NO: Return error
    → If YES: Proceed
    
  if (Guru && tipe_target = semua_siswa)
    → Override: set to per_kelas + set id_kelas ke kelas guru
  ```
- **Duplicate Prevention**: 
  ```php
  $existingTagihan = Tagihan::where('nomor_induk_siswa', $nis)
                             ->where('periode', $periode)
                             ->exists();
  
  if (!$existingTagihan) {
    // Create tagihan
  }
  ```

## Database Schema (Existing)

Menggunakan tabel `tagihan` yang sudah ada:
```sql
- id_tagihan (PK)
- nomor_induk_siswa (FK → siswa.nomor_induk_siswa)
- jumlah_tagihan (DECIMAL)
- periode (VARCHAR)
- status (VARCHAR) → belum_bayar, lunas, pending
- payment_status (VARCHAR) → belum_bayar, lunas, pending
- transaction_id (VARCHAR, nullable)
- payment_method (VARCHAR, nullable)
- payment_date (DATETIME, nullable)
- created_at, updated_at
```

## File Changes Summary

### Modified Files:
1. **`resources/views/tagihan/index.blade.php`**
   - Added "Apply All" button in page header
   - Button color: #06B6D4 (cyan)
   - Icon: bi-lightning-fill

2. **`resources/views/tagihan/bulk-create.blade.php`**
   - Redesigned with orange/cream color scheme matching dashboard
   - Added modern form styling
   - Improved preview mechanism with live updates
   - Better error handling and messages

### No Changes Needed:
- `app/Http/Controllers/TagihanController.php` - Already has complete implementation
- `app/Models/Tagihan.php` - Already has correct relationships
- `routes/web.php` - Routes already defined

## Testing Checklist

### As Guru Biasa:
- [ ] Navigate to Tagihan → Click "Apply All"
- [ ] Should only see kelas yang diajar in dropdown
- [ ] Select "Semua Siswa" → Preview shows only siswa from guru's class
- [ ] Select "Per Kelas" → Can only select guru's own class
- [ ] Try to bypass by changing form data → Should get error
- [ ] Submit valid form → Tagihan created only for guru's class students

### As Kepala Sekolah:
- [ ] Navigate to Tagihan → Click "Apply All"
- [ ] Should see ALL kelas in dropdown
- [ ] Select "Semua Siswa" → Preview shows all students in school
- [ ] Select "Per Kelas" → Can select any class
- [ ] Submit valid form → Tagihan created for all/selected students

### General:
- [ ] Duplicate prevention works (periode check)
- [ ] Success message shows correct count
- [ ] Skipped message shows if any duplicates found
- [ ] Can create multiple Apply All operations without conflicts
- [ ] Preview count matches actual created count

## Security Features

1. **Role-based Authorization**: Checked at controller level
2. **Input Validation**: All inputs validated on server-side
3. **Duplicate Prevention**: Prevents duplicate tagihan per student per period
4. **Activity Logging**: Can be added to session/audit log if needed
5. **CSRF Protection**: Form includes @csrf token

## Future Enhancements

- [ ] Export/download summary of created tagihan
- [ ] Bulk email notification to parents after Apply All
- [ ] Scheduled Apply All (recurring monthly/quarterly)
- [ ] Undo/rollback bulk operations
- [ ] Activity audit log
- [ ] Template for common periode values

## Notes

- Fitur ini menggunakan existing database schema, tidak perlu migration
- Role check sudah terintegrasi dengan session middleware `check.guru`
- Error messages dalam Bahasa Indonesia untuk user experience
- Preview mechanism helps prevent accidental bulk operations
- System automatically handles duplicate prevention
