<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;
use App\Models\Document;
use App\Models\Revision;

class ProfileController extends Controller
{
    // Menampilkan halaman utama profil
    public function profile()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        return view('profile.profile', compact('profile', 'user'));
    }

    // Menampilkan halaman dokumen
    public function showDocuments()
    {
        // Mengambil data user yang sedang login
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $document = Document::where('user_id', $user->id)->first(); // Mengambil dokumen terkait user
        $revision = Revision::where('user_id', $user->id)->latest()->first(); // Mengambil revisi terbaru

        // Mengirim data ke view
        return view('profile.documents', compact('profile', 'document', 'revision'));
    }

    // Menampilkan halaman ganti password
    public function showChangePassword()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        return view('profile.change-password', compact('profile', 'user'));
    }

    // Method untuk menambahkan atau memperbarui profil ke tabel profile
    public function storeOrUpdateProfile(Request $request)
    {
        // Validasi input dari form profil
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|digits:16|regex:/^[0-9]+$/',
            'ttl' => 'required|date',
            'gender' => 'required|in:pria,wanita',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'jalan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'nomor' => 'required|regex:/^[0-9]+$/',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        $profile->fill($request->only([
            'name', 'nik', 'ttl', 'gender', 'kecamatan', 'desa', 'jalan', 'pendidikan', 'nomor'
        ]));

        // Jika ada foto yang diunggah
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('profile_pictures', 'public');
            $profile->foto = $path;
        }

        $profile->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil disimpan.');
    }

    // Menyimpan atau memperbarui dokumen
    public function storeOrUpdateDocuments(Request $request)
    {
        // Validasi dokumen yang diunggah
        $request->validate([
            'ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
            'kk' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
            'ijazah' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
            'ak1' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:1024',
        ]);

        // Ambil dokumen terkait user saat ini
        $user = Auth::user();
        $document = Document::firstOrNew(['user_id' => $user->id]);

        // Jika KTP diunggah
        if ($request->hasFile('ktp')) {
            $ktpFile = $request->file('ktp')->store('documents', 'public');
            $document->ktp = $ktpFile;
            $document->ktp_status = 'pending'; // Reset status setelah unggah
        }

        // Jika KK diunggah
        if ($request->hasFile('kk')) {
            $kkFile = $request->file('kk')->store('documents', 'public');
            $document->kk = $kkFile;
            $document->kk_status = 'pending'; // Reset status setelah unggah
        }

        // Jika Ijazah diunggah
        if ($request->hasFile('ijazah')) {
            $ijazahFile = $request->file('ijazah')->store('documents', 'public');
            $document->ijazah = $ijazahFile;
            $document->ijazah_status = 'pending'; // Reset status setelah unggah
        }

        // Jika AK1 diunggah
        if ($request->hasFile('ak1')) {
            $ak1File = $request->file('ak1')->store('documents', 'public');
            $document->ak1 = $ak1File;
            $document->ak1_status = 'pending'; // Reset status setelah unggah
        }

        // Simpan data dokumen
        $document->save();

        return redirect()->route('profile.documents')->with('success', 'Dokumen berhasil diunggah dan menunggu konfirmasi.');
    }

    // Mengubah password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $user = Auth::user();

        // Cek apakah password saat ini benar
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Kata sandi saat ini salah.');
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    // Cek kelengkapan profil
    public function checkProfileCompletion(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Tambahkan pengecekan untuk memastikan semua field wajib terisi
        $isProfileComplete = $profile &&
            $profile->name && $profile->nik && $profile->ttl && $profile->gender &&
            $profile->kecamatan && $profile->desa && $profile->jalan &&
            $profile->pendidikan && $profile->nomor;

        return response()->json(['status' => $isProfileComplete ? 'complete' : 'incomplete']);
    }

    // Menampilkan profil pengguna dalam bentuk JSON
    public function show($user)
    {
        $profile = Profile::where('user_id', $user)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    // Preview profil pengguna
    public function preview()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Log preview data
        \Log::info('Preview Profile:', ['profile' => $profile]);

        // Cek kelengkapan profil
        $isProfileComplete = $profile && $profile->name && $profile->nik;

        if ($isProfileComplete) {
            return response()->json([
                'status' => 'complete',
                'profile' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        } else {
            return response()->json(['status' => 'incomplete']);
        }
    }

    // Menampilkan halaman preview profil
    public function showPreview()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        // Cek apakah profil ada
        if (!$profile) {
            return redirect()->route('profile')->with('error', 'Profil tidak ditemukan, silakan lengkapi terlebih dahulu.');
        }

        return view('profile.preview', compact('profile'));
    }
}
