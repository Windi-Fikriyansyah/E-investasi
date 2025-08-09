<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function index()
    {
        // Ambil data bank dari user yang login
        $banks = DB::table('bank')
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bank.index', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:50',
            'no_rekening' => 'required|string|max:30|unique:bank,no_rekening',
            'nama_pemilik' => 'required|string|max:100',
        ]);

        try {
            // Simpan ke database
            DB::table('bank')->insert([
                'id_user' => Auth::id(),
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'nama_pemilik' => $request->nama_pemilik,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('bank.index')
                ->with('success', 'Rekening bank berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan rekening bank: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Hapus hanya jika milik user yang login
            $deleted = DB::table('bank')
                ->where('id', $id)
                ->where('id_user', Auth::id())
                ->delete();

            if ($deleted) {
                return redirect()->route('bank.index')
                    ->with('success', 'Rekening bank berhasil dihapus');
            }

            return redirect()->route('bank.index')
                ->with('error', 'Gagal menghapus rekening bank atau data tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->route('bank.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
