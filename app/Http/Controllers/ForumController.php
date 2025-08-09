<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10; // Jumlah postingan per load

        // Ambil data postingan dengan paginasi
        $posts = DB::table('forum')
            ->join('users', 'forum.id_user', '=', 'users.id')
            ->select('forum.*', 'users.name as nama_user')
            ->orderBy('forum.created_at', 'desc')
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'posts' => $posts->items(),
                'next_page_url' => $posts->nextPageUrl()
            ]);
        }

        return view('forum.index', ['posts' => $posts]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'konten' => 'required|string|max:2000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Max 2MB
        ], [
            'konten.required' => 'Konten postingan tidak boleh kosong.',
            'konten.max' => 'Konten postingan maksimal 2000 karakter.',
            'gambar.image' => 'File yang diunggah harus berupa gambar.',
            'gambar.mimes' => 'Format gambar yang diizinkan: JPEG, PNG, JPG, GIF.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction(); // Mulai transaksi database

            $gambarPath = null;

            // Handle upload gambar jika ada
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');

                // Generate nama file unik
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Simpan file ke storage/app/public/forum_images
                $gambarPath = $file->storeAs('forum_images', $fileName, 'public');

                // Convert path untuk disimpan di database (dengan storage URL)
                $gambarPath = Storage::url($gambarPath);
            }

            // Insert data ke database
            $postId = DB::table('forum')->insertGetId([
                'id_user' => Auth::id(),
                'konten' => $request->konten,
                'gambar' => $gambarPath,
                'created_at' => now(),
            ]);

            // Tambah balance user sebesar 2000
            DB::table('users')
                ->where('id', Auth::id())
                ->increment('balance', 2000);

            DB::commit(); // Commit transaksi jika semua operasi berhasil

            return response()->json([
                'success' => true,
                'message' => 'Postingan berhasil dipublikasikan dan balance bertambah 2000!',
                'data' => [
                    'id' => $postId,
                    'konten' => $request->konten,
                    'gambar' => $gambarPath,
                    'created_at' => now()->format('Y-m-d H:i:s')
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error

            // Log error untuk debugging
            \Log::error('Forum store error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan postingan. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
