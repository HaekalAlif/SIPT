<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisTagihan;
use App\Models\KategoriTagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalSantri = User::where('role', User::ROLE_SANTRI)->count();
        $totalBendahara = User::where('role', User::ROLE_BENDAHARA)->count();
        $totalPendapatan = Pembayaran::where('status', Pembayaran::STATUS_DITERIMA)
            ->whereMonth('created_at', now()->month)
            ->sum('jumlah_bayar');
        $totalTagihan = Tagihan::count();

        return view('admin.dashboard', compact(
            'totalSantri',
            'totalBendahara', 
            'totalPendapatan',
            'totalTagihan'
        ));
    }

    // === CRUD Users ===
    public function indexUsers()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nama_santri' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,bendahara,santri',
            'nis' => 'nullable|string|max:30|unique:users,nis',
            'status' => 'required|in:active,inactive'
        ]);

        User::create([
            'nama_santri' => $request->nama_santri,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nis' => $request->nis,
            'status' => $request->status,
            'nama_orang_tua' => $request->nama_orang_tua,
            'no_telp' => $request->no_telp,
            'tingkatan' => $request->tingkatan,
            'kelas' => $request->kelas,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'tingkatan_ngaji' => $request->tingkatan_ngaji,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function showUser($id)
    {
        $user = User::with(['kartuPembayaran.tagihan.pembayaran'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nama_santri' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,bendahara,santri',
            'nis' => 'nullable|string|max:30|unique:users,nis,' . $id,
            'status' => 'required|in:active,inactive'
        ]);

        $updateData = $request->only([
            'nama_santri', 'email', 'role', 'nis', 'status',
            'nama_orang_tua', 'no_telp', 'tingkatan', 'kelas',
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'alamat', 'tingkatan_ngaji'
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus!');
    }

    // === CRUD Tahun Ajaran ===
    public function indexTahunAjaran()
    {
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function createTahunAjaran()
    {
        return view('admin.tahun-ajaran.create');
    }

    public function storeTahunAjaran(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajaran,nama',
            'is_active' => 'boolean'
        ]);

        // Jika tahun ajaran baru diaktifkan, nonaktifkan yang lain
        if ($request->is_active) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        TahunAjaran::create($request->all());

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil ditambahkan!');
    }

    public function editTahunAjaran($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function updateTahunAjaran(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:20|unique:tahun_ajaran,nama,' . $id,
            'is_active' => 'boolean'
        ]);

        // Jika tahun ajaran ini diaktifkan, nonaktifkan yang lain
        if ($request->is_active) {
            TahunAjaran::where('id', '!=', $id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $tahunAjaran->update($request->all());

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil diupdate!');
    }

    public function destroyTahunAjaran($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil dihapus!');
    }

    // === CRUD Kategori Tagihan ===
    public function indexKategoriTagihan()
    {
        $kategoris = KategoriTagihan::withCount('jenisTagihan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.kategori-tagihan.index', compact('kategoris'));
    }

    public function createKategoriTagihan()
    {
        return view('admin.kategori-tagihan.create');
    }

    public function storeKategoriTagihan(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:kategori_tagihan,nama'
        ]);

        KategoriTagihan::create($request->all());

        return redirect()->route('admin.kategori-tagihan.index')
            ->with('success', 'Kategori Tagihan berhasil ditambahkan!');
    }

    public function editKategoriTagihan($id)
    {
        $kategori = KategoriTagihan::findOrFail($id);
        return view('admin.kategori-tagihan.edit', compact('kategori'));
    }

    public function updateKategoriTagihan(Request $request, $id)
    {
        $kategori = KategoriTagihan::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:50|unique:kategori_tagihan,nama,' . $id
        ]);

        $kategori->update($request->all());

        return redirect()->route('admin.kategori-tagihan.index')
            ->with('success', 'Kategori Tagihan berhasil diupdate!');
    }

    public function destroyKategoriTagihan($id)
    {
        $kategori = KategoriTagihan::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.kategori-tagihan.index')
            ->with('success', 'Kategori Tagihan berhasil dihapus!');
    }

    // === CRUD Jenis Tagihan ===
    public function indexJenisTagihan()
    {
        $jenisTagihans = JenisTagihan::with('kategori')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.jenis-tagihan.index', compact('jenisTagihans'));
    }

    public function createJenisTagihan()
    {
        $kategoris = KategoriTagihan::all();
        return view('admin.jenis-tagihan.create', compact('kategoris'));
    }

    public function storeJenisTagihan(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihan,id',
            'nama_tagihan' => 'required|string|max:150',
            'nominal' => 'required|numeric|min:0',
            'is_bulanan' => 'boolean'
        ]);

        JenisTagihan::create($request->all());

        return redirect()->route('admin.jenis-tagihan.index')
            ->with('success', 'Jenis Tagihan berhasil ditambahkan!');
    }

    public function editJenisTagihan($id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        $kategoris = KategoriTagihan::all();
        return view('admin.jenis-tagihan.edit', compact('jenisTagihan', 'kategoris'));
    }

    public function updateJenisTagihan(Request $request, $id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        
        $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihan,id',
            'nama_tagihan' => 'required|string|max:150',
            'nominal' => 'required|numeric|min:0',
            'is_bulanan' => 'boolean'
        ]);

        $jenisTagihan->update($request->all());

        return redirect()->route('admin.jenis-tagihan.index')
            ->with('success', 'Jenis Tagihan berhasil diupdate!');
    }

    public function destroyJenisTagihan($id)
    {
        $jenisTagihan = JenisTagihan::findOrFail($id);
        $jenisTagihan->delete();

        return redirect()->route('admin.jenis-tagihan.index')
            ->with('success', 'Jenis Tagihan berhasil dihapus!');
    }
}