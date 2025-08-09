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
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('status', 'active')->count();
        $inactiveUsers = User::where('role', 'user')->where('status', 'inactive')->count();
        $totalBalance = User::where('role', 'user')->sum('balance');

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
                    'phone' => $user->phone,
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
            ->select(['id', 'phone', 'email', 'balance', 'status', 'keanggotaan'])
            ->latest();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                $toggleUrl = route('admin.user.toggle-status', $user->id);
                $detailUrl = route('admin.user.show', Crypt::encrypt($user->id));

                $buttons = '<div class="action-buttons">';
                $buttons .= '<a href="' . $detailUrl . '" class="btn btn-sm btn-info detail-btn"><i class="bi bi-eye-fill"></i></a>';

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
}
