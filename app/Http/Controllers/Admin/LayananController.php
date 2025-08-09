<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\ApiSupplier;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LayananController extends Controller
{
    public function index()
    {
        $suppliers = ApiSupplier::where('status', 1)->get();
        $kategoris = Service::select('category')->distinct()->pluck('category');
        $types = Service::select('type')->distinct()->pluck('type');
        return view('admin.layanan.index', compact('suppliers', 'types', 'kategoris'));
    }

    public function load(Request $request)
    {
        try {
            $services = DB::table('services')
                ->join('api_suppliers', 'services.supplier_id', '=', 'api_suppliers.id')
                ->select([
                    'services.id',
                    'services.service_api_id',
                    'services.name',
                    'api_suppliers.name as name_supplier',
                    'services.type',
                    'services.category',
                    'services.rate',
                    'services.min',
                    'services.max',
                    'services.status'
                ]);

            // Add search filters
            if ($request->has('service_id') && !empty($request->service_id)) {
                $services->where('services.service_api_id', 'like', '%' . $request->service_id . '%');
            }

            if ($request->has('name') && !empty($request->name)) {
                $services->where('services.name', 'like', '%' . $request->name . '%');
            }

            if ($request->has('category') && !empty($request->category)) {
                $services->where('services.category', $request->category);
            }

            if ($request->has('type') && !empty($request->type)) {
                $services->where('services.type', $request->type);
            }

            return DataTables::of($services)
                ->addIndexColumn()
                ->editColumn('rate', function ($service) {
                    return 'Rp ' . number_format($service->rate, 0, ',', '.');
                })
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function sync(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:api_suppliers,id'
        ]);

        try {
            $supplier = ApiSupplier::findOrFail($request->supplier_id);


            // Parameter yang dikirim ke API, sesuaikan jika perlu
            $params = [
                'key' => $supplier->api_key,
                'action' => $supplier->api_services_action ?? 'services'
            ];

            // Panggil API supplier
            $response = match ($supplier->api_format) {
                'json' => Http::timeout(15)->post($supplier->api_url, $params),
                'form' => Http::timeout(15)->asForm()->post($supplier->api_url, $params),
                'get'  => Http::timeout(15)->get($supplier->api_url, $params),
                default => throw new \Exception('Format API tidak dikenali')
            };


            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil data dari API supplier: HTTP ' . $response->status());
            }

            $apiServices = $response->json();


            // Tangani jika data berada di key tertentu seperti 'data'
            if (isset($apiServices['data'])) {
                $apiServices = $apiServices['data'];
            }

            if (!is_array($apiServices) || empty($apiServices)) {
                throw new \Exception('Format data tidak valid atau kosong');
            }

            // Proses dan simpan data layanan
            $count = 0;
            DB::beginTransaction();

            try {
                // Hapus layanan lama dari supplier ini
                Service::where('supplier_id', $supplier->id)->delete();

                foreach ($apiServices as $apiService) {


                    $serviceData = [
                        'supplier_id'     => $supplier->id,
                        'service_api_id'  => $apiService['service'],
                        'name'            => $apiService['name'],
                        'type'            => $apiService['type'] ?? 'Default',
                        'category'        => $apiService['category'] ?? 'Uncategorized',
                        'rate'            => $apiService['rate'] ?? 0,
                        'min'             => $apiService['min'] ?? 0,
                        'max'             => $apiService['max'] ?? 0,
                        'refill'          => $apiService['refill'] ?? false,
                        'cancel'          => $apiService['cancel'] ?? false,
                        'status'          => 1
                    ];


                    Service::create($serviceData);
                    $count++;
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menyinkronkan $count layanan dari " . $supplier->name,
                    'data' => ['count' => $count]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Sync services error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyinkronkan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $service = Service::findOrFail($request->id);
            $service->status = $request->status;
            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
