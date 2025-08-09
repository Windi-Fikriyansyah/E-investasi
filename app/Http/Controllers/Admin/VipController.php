<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;


class VipController extends Controller
{
    public function index()
    {
        return view('admin.vip.index');
    }

    public function create()
    {
        return view('admin.vip.create');
    }

    public function edit($id)
    {
        try {
            // Dekripsi ID
            $decryptedId = Crypt::decrypt($id);

            $vip = DB::table('vip')->where('id', $decryptedId)->first();

            if (!$vip) {
                return redirect()->route('admin.vip.index')
                    ->with('error', 'vip tidak ditemukan');
            }

            return view('admin.vip.create', compact('vip'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Decrypt error: ' . $e->getMessage());
            return redirect()->route('admin.vip.index')
                ->with('error', 'ID vip tidak valid');
        } catch (\Exception $e) {
            Log::error('Error fetching vip: ' . $e->getMessage());
            return redirect()->route('admin.vip.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data vip');
        }
    }
    public function load(Request $request)
    {
        try {
            $vip = DB::table('vip')
                ->select([
                    'id',
                    'vip'
                ]);

            return DataTables::of($vip)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $editUrl = route('admin.vip.edit', $encryptedId);
                    $deleteUrl = route('admin.vip.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading vip data: ' . $e->getMessage());
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
                'vip' => 'required|string|max:255'
            ]);

            DB::table('vip')->insert($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'vip berhasil ditambahkan'
                ]);
            }

            return redirect()->route('admin.vip.index')
                ->with('success', 'vip berhasil ditambahkan');
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
            Log::error('Error storing vip: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan vip')
                ->withInput();
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'vip' => 'required|string|max:255'
            ]);

            $vip = DB::table('vip')->where('id', $id)->first();

            if (!$vip) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'vip tidak ditemukan'
                    ], 404);
                }

                return redirect()->route('admin.vip.index')
                    ->with('error', 'vip tidak ditemukan');
            }



            DB::table('vip')->where('id', $id)->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'vip berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.vip.index')
                ->with('success', 'vip berhasil diperbarui');
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
            Log::error('Error updating vip: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui vip')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $vip = DB::table('vip')->where('id', $id)->first();

            if (!$vip) {
                return response()->json([
                    'error' => true,
                    'message' => 'vip tidak ditemukan'
                ], 404);
            }

            DB::table('vip')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'vip berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting vip: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
