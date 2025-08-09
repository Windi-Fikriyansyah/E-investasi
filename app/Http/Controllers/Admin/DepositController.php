<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class DepositController extends Controller
{
    public function index()
    {
        return view('admin.deposit.index');
    }

    public function load(Request $request)
    {
        try {
            $deposits = DB::table('deposits')
                ->join('users', 'deposits.user_id', '=', 'users.id')
                ->select([
                    'deposits.id',
                    'users.name as user_name',
                    'deposits.order_id',
                    'deposits.amount',
                    'deposits.payment_method',
                    'deposits.status',
                    'deposits.created_at'
                ]);

            // Add search if there is a search parameter
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = strtolower($request->search['value']);
                $deposits->where(function ($query) use ($search) {
                    $query->where(DB::raw('LOWER(users.name)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(deposits.order_id)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(deposits.amount)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(deposits.payment_method)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(deposits.status)'), 'like', "%{$search}%")
                        ->orWhere(DB::raw('LOWER(deposits.created_at)'), 'like', "%{$search}%");
                });
            }

            $deposits->orderByRaw("FIELD(deposits.status, 'processing', 'pending', 'success', 'rejected')")
                ->orderBy('deposits.created_at', 'asc');

            return DataTables::of($deposits)
                ->addIndexColumn()
                ->rawColumns(['status'])
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
            DB::table('deposits')
                ->where('id', $id)
                ->update(['status' => 'success', 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyelesaikan deposit: ' . $e->getMessage()
            ], 500);
        }
    }
}
