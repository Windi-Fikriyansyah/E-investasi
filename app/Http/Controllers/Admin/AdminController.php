<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin.index');
    }

    public function load(Request $request)
    {
        try {
            $currentUserId = auth()->id();
            $users = User::where('role', 'admin') // Filter hanya user dengan role admin
                ->select([
                    'id',
                    'name',
                    'email',
                    'username',
                    'created_at'
                ]);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($currentUserId) {
                    $encryptedId = Crypt::encrypt($row->id);
                    $editUrl = route('admin.admin.edit', $encryptedId);
                    $deleteUrl = route('admin.admin.destroy', $row->id);

                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-info me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                    if ($row->id != $currentUserId) {
                        $buttons .= '<button class="btn btn-sm btn-danger delete-btn" title="Hapus" data-id="' . $row->id . '" data-url="' . $deleteUrl . '"><i class="bi bi-trash"></i></button>';
                    }
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error loading admin data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        return view('admin.admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'status' => 'active'
            ]);

            return redirect()->route('admin.admin.index')->with('success', 'Admin berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan admin: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $user = User::findOrFail($decryptedId);
            return view('admin.admin.create', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('admin.admin.index')->with('error', 'Admin tidak ditemukan');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $user = User::findOrFail($id);

            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('admin.admin.index')->with('success', 'Admin berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui admin: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $users = DB::table('users')->where('id', $id)->first();

            if (!$users) {
                return response()->json([
                    'error' => true,
                    'message' => 'users tidak ditemukan'
                ], 404);
            }

            DB::table('users')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'users berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting users: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
