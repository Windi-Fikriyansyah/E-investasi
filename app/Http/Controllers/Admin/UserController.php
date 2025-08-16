<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $totalBalance = User::sum('balance');

        return view('admin.users.index', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalBalance'
        ));
    }

    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $user = User::findOrFail($decryptedId);

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'saldo' => $user->balance ?? 0,
                    'status' => $user->status == 'active' ? 'Aktif' : 'Nonaktif',
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/images/faces/1.jpg')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $newStatus = $user->status == 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus == 'active' ? 'Aktif' : 'Nonaktif'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function load(Request $request)
    {
        $users = User::query()
            ->where('role', 'user')
            ->select(['id', 'name', 'email', 'phone', 'balance', 'status', 'keanggotaan'])
            ->latest();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                $toggleUrl = route('admin.user.toggle-status', $user->id);
                $detailUrl = route('admin.user.show', Crypt::encrypt($user->id));
                $addBalanceUrl = route('admin.user.update-balance', $user->id);

                $buttons = '<div class="action-buttons">';
                $buttons .= '<a href="' . $detailUrl . '" class="btn btn-sm btn-info detail-btn"><i class="bi bi-eye-fill"></i></a>';
                $buttons .= '<button class="btn btn-sm btn-primary add-balance-btn" data-url="' . $addBalanceUrl . '" data-id="' . $user->id . '" title="Tambah Saldo"><i class="bi bi-wallet2"></i></button>';
                $buttons .= '<button class="btn btn-sm btn-warning edit-password-btn" data-id="' . $user->id . '" title="Edit Password"><i class="bi bi-key-fill"></i></button>';
                if ($user->status == 'active') {
                    $buttons .= '<button class="btn btn-sm btn-danger toggle-status-btn" data-url="' . $toggleUrl . '" data-id="' . $user->id . '" data-status="Aktif" title="Nonaktifkan"><i class="bi bi-power"></i></button>';
                } else {
                    $buttons .= '<button class="btn btn-sm btn-success toggle-status-btn" data-url="' . $toggleUrl . '" data-id="' . $user->id . '" data-status="Nonaktif" title="Aktifkan"><i class="bi bi-power"></i></button>';
                }

                $buttons .= '</div>';

                return $buttons;
            })
            ->editColumn('status', function ($user) {
                if ($user->status == "active") {
                    return '<span class="badge bg-success">Aktif</span>';
                } elseif ($user->status == "inactive") {
                    return '<span class="badge bg-danger">Nonaktif</span>';
                } else {
                    return '<span class="badge bg-secondary">Banned</span>';
                }
            })
            ->editColumn('balance', function ($user) {
                return 'Rp ' . number_format($user->balance, 0, ',', '.');
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function updatePassword(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ], [
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sama',
            ]);

            $user = User::findOrFail($id);
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->validator ? $e->validator->errors() : []
            ], 500);
        }
    }

    // Add this method to your UserController
    public function updateBalance(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1000',
            ], [
                'amount.required' => 'Jumlah saldo wajib diisi',
                'amount.numeric' => 'Jumlah harus berupa angka',
                'amount.min' => 'Minimal penambahan saldo adalah 1000',
            ]);

            $user = User::findOrFail($id);
            $user->increment('balance', $request->amount);

            return response()->json([
                'success' => true,
                'message' => 'Saldo berhasil ditambahkan',
                'new_balance' => $user->balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->validator ? $e->validator->errors() : []
            ], 500);
        }
    }
}
