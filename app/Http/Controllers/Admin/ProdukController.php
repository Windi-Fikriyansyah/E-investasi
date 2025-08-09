<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;


class ProdukController extends Controller
{
    public function index()
    {
        return view('admin.produk.index');
    }

    public function create()
    {
        return view('admin.produk.create');
    }

    public function edit($id)
    {
        try {
            // Dekripsi ID
            $decryptedId = Crypt::decrypt($id);

            $produk = DB::table('produk')->where('id', $decryptedId)->first();

            if (!$produk) {
                return redirect()->route('admin.produk.index')
                    ->with('error', 'Produk tidak ditemukan');
            }

            return view('admin.produk.create', compact('produk'));
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Decrypt error: ' . $e->getMessage());
            return redirect()->route('admin.produk.index')
                ->with('error', 'ID produk tidak valid');
        } catch (\Exception $e) {
            Log::error('Error fetching produk: ' . $e->getMessage());
            return redirect()->route('admin.produk.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data produk');
        }
    }
    public function load(Request $request)
    {
        try {
            $produk = DB::table('produk')
                ->select([
                    'id',
                    'nama_produk',
                    'harga',
                    'durasi',
                    'pendapatan_harian',
                    'kategori'
                ]);

            return DataTables::of($produk)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $editUrl = route('admin.produk.edit', $encryptedId);
                    $deleteUrl = route('admin.produk.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->editColumn('harga', function ($row) {
                    return 'Rp ' . number_format($row->harga, 0, ',', '.');
                })
                ->editColumn('pendapatan_harian', function ($row) {
                    return 'Rp ' . number_format($row->pendapatan_harian, 0, ',', '.');
                })
                ->editColumn('durasi', function ($row) {
                    return $row->durasi . ' hari';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading produk data: ' . $e->getMessage());
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
                'nama_produk' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'durasi' => 'required|integer|min:1',
                'pendapatan_harian' => 'required|numeric|min:0',
                'total_pendapatan' => 'required|numeric|min:0',
                'kategori' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/produk', $imageName);
                $validated['gambar'] = 'produk/' . $imageName;
            }

            $validated['created_at'] = now();
            $validated['updated_at'] = now();

            DB::table('produk')->insert($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan'
                ]);
            }

            return redirect()->route('admin.produk.index')
                ->with('success', 'Produk berhasil ditambahkan');
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
            Log::error('Error storing produk: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan produk')
                ->withInput();
        }
    }

    public function getKategori(Request $request)
    {
        try {
            $search = $request->get('q');

            $query = DB::table('kategori')
                ->select('kategori as text')
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '');

            if ($search) {
                $query->where('kategori', 'like', '%' . $search . '%');
            }

            $kategori = $query->distinct()->get();

            return response()->json($kategori);
        } catch (\Exception $e) {
            Log::error('Error fetching kategori: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengambil data kategori'
            ], 500);
        }
    }

    // public function getvip(Request $request)
    // {
    //     try {
    //         $search = $request->get('q');

    //         $query = DB::table('vip')
    //             ->select('vip as text')
    //             ->whereNotNull('vip')
    //             ->where('vip', '!=', '');

    //         if ($search) {
    //             $query->where('vip', 'like', '%' . $search . '%');
    //         }

    //         $vip = $query->distinct()->get();

    //         return response()->json($vip);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching vip: ' . $e->getMessage());
    //         return response()->json([
    //             'error' => true,
    //             'message' => 'Terjadi kesalahan saat mengambil data vip'
    //         ], 500);
    //     }
    // }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_produk' => 'required|string|max:255',
                'harga' => 'required|numeric|min:0',
                'durasi' => 'required|integer|min:1',
                'pendapatan_harian' => 'required|numeric|min:0',
                'total_pendapatan' => 'required|numeric|min:0',
                'kategori' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $produk = DB::table('produk')->where('id', $id)->first();

            if (!$produk) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Produk tidak ditemukan'
                    ], 404);
                }

                return redirect()->route('admin.produk.index')
                    ->with('error', 'Produk tidak ditemukan');
            }

            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($produk->gambar) {
                    Storage::delete('public/' . $produk->gambar);
                }

                $image = $request->file('gambar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/produk', $imageName);
                $validated['gambar'] = 'produk/' . $imageName;
            }


            $validated['updated_at'] = now();

            DB::table('produk')->where('id', $id)->update($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.produk.index')
                ->with('success', 'Produk berhasil diperbarui');
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
            Log::error('Error updating produk: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui produk')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $produk = DB::table('produk')->where('id', $id)->first();

            if (!$produk) {
                return response()->json([
                    'error' => true,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            if ($produk->gambar) {
                Storage::delete('public/' . $produk->gambar);
            }


            DB::table('produk')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting produk: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
