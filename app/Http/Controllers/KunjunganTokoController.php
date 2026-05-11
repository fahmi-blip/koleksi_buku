<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\VisitLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Picqer\Barcode\Renderers\SvgRenderer;
use Picqer\Barcode\Types\TypeCode128;

class KunjunganTokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::query()->orderBy('name', 'asc')->get();
        return view('pages.kunjungan_toko', compact('tokos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
        ]);

        $data['barcode'] = $this->generateUniqueBarcode();

        Toko::create($data);

        return redirect()->route('kunjungan.index')->with('success', 'Toko disimpan');
    }

    public function barcode(Toko $toko)
    {
        $barcodeHtml = $this->renderBarcode($toko->barcode);

        return view('pages.toko_barcode', [
            'toko' => $toko,
            'barcodeHtml' => $barcodeHtml,
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:100',
        ]);

        $barcode = trim($validated['barcode']);
        $toko = Toko::query()->where('barcode', '=', $barcode)->first();

        if (! $toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $toko->id,
                'barcode' => $toko->barcode,
                'name' => $toko->name,
                'latitude' => (float) $toko->latitude,
                'longitude' => (float) $toko->longitude,
                'accuracy' => (float) $toko->accuracy,
            ],
        ]);
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'threshold' => 'nullable|numeric',
        ]);

        $toko = Toko::query()->where('barcode', '=', $data['barcode'])->first();
        if (! $toko) {
            return response()->json(['ok' => false, 'message' => 'Toko tidak ditemukan'], 404);
        }

        $threshold = $data['threshold'] ?? 300; // default 300 m

        $distance = $this->haversine($toko->latitude, $toko->longitude, $data['latitude'], $data['longitude']);

        $effective = $threshold + floatval($toko->accuracy) + floatval($data['accuracy']);

        $status = ($distance <= $effective) ? 'accepted' : 'rejected';

        // simpan log
        VisitLog::create([
            'toko_id' => $toko->id,
            'user_id' => Auth::id(),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'],
            'distance' => $distance,
            'threshold' => $threshold,
            'status' => $status,
        ]);

        return response()->json([
            'ok' => true,
            'status' => $status,
            'distance' => $distance,
            'distance_text' => number_format($distance, 2, ',', '.'),
            'effective_threshold' => $effective,
            'effective_threshold_text' => number_format($effective, 2, ',', '.'),
            'toko' => [
                'name' => $toko->name,
                'barcode' => $toko->barcode,
                'latitude' => (float) $toko->latitude,
                'longitude' => (float) $toko->longitude,
                'accuracy' => (float) $toko->accuracy,
            ],
            'visit' => [
                'latitude' => (float) $data['latitude'],
                'longitude' => (float) $data['longitude'],
                'accuracy' => (float) $data['accuracy'],
            ],
        ]);
    }

    private function generateUniqueBarcode(): string
    {
        do {
            $barcode = Str::upper(Str::random(8));
        } while (Toko::query()->where('barcode', '=', $barcode)->exists());

        return $barcode;
    }

    private function renderBarcode(string $value): string
    {
        $barcode = (new TypeCode128())->getBarcode($value);

        return (new SvgRenderer())->render($barcode);
    }

    private function haversine($lat1, $lng1, $lat2, $lng2)
    {
        $R = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
