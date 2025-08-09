<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class WithdrawController extends Controller
{
    public function index()
    {
        return view('admin.withdraw.index');
    }

    public function load(Request $request)
    {
        try {
            $withdrawals = DB::table('withdrawals')
                ->join('users', 'withdrawals.user_id', '=', 'users.id')
                ->select([
                    'withdrawals.id',
                    'users.phone',
                    'withdrawals.bank_name',
                    'withdrawals.bank_number',
                    'withdrawals.bank_account',
                    'withdrawals.status',
                    'withdrawals.amount',
                    'withdrawals.admin_fee',
                    'withdrawals.created_at',
                    DB::raw('(withdrawals.amount - withdrawals.admin_fee) as net_amount')
                ]);

            // Tambahkan pencarian jika ada parameter search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = strtolower($request->search['value']);
                $withdrawals->where(function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(users.phone)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(withdrawals.bank_name)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(withdrawals.bank_number)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(withdrawals.bank_account)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(withdrawals.status)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(withdrawals.amount - withdrawals.admin_fee)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(withdrawals.created_at)'), 'like', "%{$search}%");
                });
            }

            $withdrawals->orderByRaw("FIELD(withdrawals.status, 'processing', 'pending', 'completed', 'rejected')")
                ->orderBy('withdrawals.created_at', 'asc');

            return DataTables::of($withdrawals)
                ->filterColumn('net_amount', function ($query, $keyword) {
                    $query->whereRaw('(withdrawals.amount - withdrawals.admin_fee) like ?', ["%{$keyword}%"]);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->status === 'processing') {
                        return '<button class="btn btn-sm btn-success complete-btn" data-id="' . $row->id . '">
                    <i class="bi bi-check-circle"></i> Selesaikan
                </button>';
                    }
                    return '';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete($id)
    {
        try {
            DB::table('withdrawals')
                ->where('id', $id)
                ->update(['status' => 'success', 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Withdraw berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyelesaikan withdraw: ' . $e->getMessage()
            ], 500);
        }
    }
}
