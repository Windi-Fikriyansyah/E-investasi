<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MidtransSetting;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class SettingMidtransController extends Controller
{
    public function index(Request $request)
    {

        $isEmpty = MidtransSetting::count() === 0;
        return view('admin.midtrans_settings.index', compact('isEmpty'));
    }

    public function load(Request $request)
    {
        try {
            $midtrans_settings = DB::table('midtrans_settings')
                ->select([
                    'id',
                    'server_key',
                    'client_key',
                    'mode',
                    'created_at',
                    'updated_at'
                ]);

            return DataTables::of($midtrans_settings)
                ->addColumn('action', function ($setting) {
                    return '<a href="' . route('admin.midtrans_settings.edit', $setting->id) . '" class="btn btn-sm btn-warning edit-btn" data-id="' . $setting->id . '"><i class="bi bi-pencil-square"></i></a>
                        <button class="btn btn-sm btn-danger delete-btn" data-url="' . route('admin.midtrans_settings.destroy', $setting->id) . '"><i class="bi bi-trash"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {
        $request->validate([
            'server_key' => 'required|string',
            'client_key' => 'required|string',
            'mode' => 'required|in:sandbox,production'
        ]);

        // Hanya boleh ada satu setting
        MidtransSetting::query()->delete();

        $setting = MidtransSetting::create($request->only(['server_key', 'client_key', 'mode']));

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully',
            'data' => $setting
        ]);
    }

    public function edit($id)
    {
        $setting = MidtransSetting::findOrFail($id);

        return response()->json($setting);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'server_key' => 'required|string',
            'client_key' => 'required|string',
            'mode' => 'required|in:sandbox,production'
        ]);

        $setting = MidtransSetting::findOrFail($id);
        $setting->update($request->only(['server_key', 'client_key', 'mode']));

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $setting
        ]);
    }

    public function destroy($id)
    {
        try {
            $setting = MidtransSetting::findOrFail($id);
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Settings deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
