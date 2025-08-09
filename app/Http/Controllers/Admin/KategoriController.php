<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;


class KategoriController extends Controller
{
    public function index()
    {
        return view('admin.kategori.index');
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function edit($id)
    {
        try {
            // Dekripsi ID
            $decryptedId = Crypt::decrypt($id);

            $kategori = DB::table('kategori')->where('id', $decryptedId)->first();

            if (!$kategori) {
                return redirect()->route('admin.kategori.index')
                    ->with('error', 'kategori tidak ditemukan');
            }

            return view('admin.kategori.create', compact('kategori'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Decrypt error: ' . $e->getMessage());
            return redirect()->route('admin.kategori.index')
                ->with('error', 'ID kategori tidak valid');
        } catch (\Exception $e) {
            Log::error('Error fetching kategori: ' . $e->getMessage());
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data kategori');
        }
    }
    public function load(Request $request)
    {
        try {
            $kategori = DB::table('kategori')
                ->select([
                    'id',
                    'kategori'
                ]);

            return DataTables::of($kategori)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $editUrl = route('admin.kategori.edit', $encryptedId);
                    $deleteUrl = route('admin.kategori.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading kategori data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'kategori' => 'required|string|max:255'
            ]);

            DB::table('kategori')->insert($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'kategori berhasil ditambahkan'
                ]);
            }

            return redirect()->route('admin.kategori.index')
                ->with('success', 'kategori berhasil ditambahkan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error storing kategori: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan kategori')
                ->withInput();
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'kategori' => 'required|string|max:255'
            ]);

            $kategori = DB::table('kategori')->where('id', $id)->first();

            if (!$kategori) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'kategori tidak ditemukan'
                    ], 404);
                }

                return redirect()->route('admin.kategori.index')
                    ->with('error', 'kategori tidak ditemukan');
            }



            DB::table('kategori')->where('id', $id)->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'kategori berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.kategori.index')
                ->with('success', 'kategori berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating kategori: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui kategori')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = DB::table('kategori')->where('id', $id)->first();

            if (!$kategori) {
                return response()->json([
                    'error' => true,
                    'message' => 'kategori tidak ditemukan'
                ], 404);
            }

            DB::table('kategori')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting kategori: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
