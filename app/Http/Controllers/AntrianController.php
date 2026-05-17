<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AntrianController extends Controller
{
    private function buildQueueState(): array
    {
        $antrians = DB::table('antrians')->orderBy('nomor')->get()->map(function ($antrian) {
            return $this->normalizeAntrianRow($antrian);
        })->values();

        $called = DB::table('antrians')
            ->where('status', 'dipanggil')
            ->orderByDesc('called_at')
            ->first();

        if ($called) {
            $called = $this->normalizeAntrianRow($called);
        }

        return [
            'antrians' => $antrians,
            'called' => $called,
        ];
    }

    private function layananLabelMap(): array
    {
        return [
            'loket_umum' => 'loket umum',
            'loket_khusus' => 'loket khusus',
            'informasi' => 'informasi',
        ];
    }

    private function formatLayananText($raw): string
    {
        $labels = $this->layananLabelMap();

        if (is_array($raw)) {
            $items = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = preg_split('/\s*,\s*/', $raw) ?: [];
            }
        } else {
            $items = [];
        }

        $items = array_values(array_filter(array_map(function ($item) use ($labels) {
            $key = is_string($item) ? trim($item) : '';
            return $labels[$key] ?? ($key !== '' ? str_replace('_', ' ', $key) : null);
        }, $items)));

        if (empty($items)) {
            return '-';
        }

        if (count($items) === 1) {
            return $items[0];
        }

        $last = array_pop($items);
        return implode(', ', $items) . ' dan ' . $last;
    }

    private function formatAnnouncement(object $antrian): string
    {
        $nomor = str_pad((string) $antrian->nomor, 3, '0', STR_PAD_LEFT);
        $name = trim((string) $antrian->name);
        $layananText = $this->formatLayananText($antrian->layanan ?? null);

        if ($layananText === '-' || $layananText === '') {
            return 'Nomor antrean ' . $nomor . ' atas nama ' . $name . '. Silakan menuju ke petugas.';
        }

        return 'Nomor antrean ' . $nomor . ' atas nama ' . $name . '. Silakan menuju ke layanan ' . $layananText . '.';
    }

    private function normalizeAntrianRow(object $antrian): object
    {
        $antrian->nomor_text = str_pad((string) $antrian->nomor, 3, '0', STR_PAD_LEFT);
        $antrian->layanan_text = $this->formatLayananText($antrian->layanan ?? null);
        $antrian->announcement_text = $this->formatAnnouncement($antrian);
        return $antrian;
    }

    public function guest()
    {
        return view('antrian.guest');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'layanan' => 'nullable',
        ]);

        $max = Antrian::max('nomor') ?: 0;
        $nomor = $max + 1;

        $antrian = Antrian::create([
            'nomor' => $nomor,
            'name' => $request->name,
            'layanan' => is_array($request->layanan) ? json_encode($request->layanan) : ($request->layanan ?? null),
            'status' => 'menunggu',
        ]);

        try {
            Log::info('Antrian created', ['id' => $antrian->id, 'nomor' => $nomor]);
        } catch (\Throwable $e) {}

        Cache::put('antrian_last_update', time());

        return response()->json([
            'success' => true,
            'id' => $antrian->id,
            'nomor' => $nomor,
            'nomor_text' => str_pad((string) $nomor, 3, '0', STR_PAD_LEFT),
            'name' => $antrian->name,
            'layanan_text' => $this->formatLayananText($antrian->layanan),
        ]);
    }

    public function ticket(Antrian $antrian)
    {
        return view('antrian.ticket', compact('antrian'));
    }

    public function admin()
    {
        $state = $this->buildQueueState();
        $antrians = $state['antrians'];
        $called = $state['called'];
        return view('antrian.admin', compact('antrians', 'called'));
    }

    public function snapshot()
    {
        $state = $this->buildQueueState();

        return response()->json([
            'success' => true,
            'antrians' => $state['antrians'],
            'called' => $state['called'],
        ]);
    }

    public function call(Request $request, Antrian $antrian)
    {
        DB::table('antrians')->where('id', $antrian->id)->update([
            'status' => 'dipanggil',
            'called_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        $called = DB::table('antrians')->where('id', $antrian->id)->first();
        $called = $this->normalizeAntrianRow($called);

        try { Log::info('Antrian called', ['id' => $antrian->id]); } catch (\Throwable $e) {}
        Cache::put('antrian_last_update', time());

        return response()->json([
            'success' => true,
            'called' => $called,
        ]);
    }

    public function finish(Request $request, Antrian $antrian)
    {
        DB::table('antrians')->where('id', $antrian->id)->update([
            'status' => 'selesai',
            'updated_at' => now()->toDateTimeString(),
        ]);
        try { Log::info('Antrian finished', ['id' => $antrian->id]); } catch (\Throwable $e) {}
        Cache::put('antrian_last_update', time());
        return response()->json(['success' => true]);
    }

    public function late(Request $request, Antrian $antrian)
    {
        DB::table('antrians')->where('id', $antrian->id)->update([
            'status' => 'terlambat',
            'updated_at' => now()->toDateTimeString(),
        ]);
        try { Log::info('Antrian late', ['id' => $antrian->id]); } catch (\Throwable $e) {}
        Cache::put('antrian_last_update', time());
        return response()->json(['success' => true]);
    }

    public function sse()
    {
        return response()->stream(function () {
            @set_time_limit(0);
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            ignore_user_abort(true);

            $lastState = null;
            $lastCache = Cache::get('antrian_last_update', 0);
            $lastPingAt = 0;

            while (!connection_aborted()) {
                // If first connection, send initial snapshot immediately
                if ($lastState === null) {
                    try {
                        $state = $this->buildQueueState();
                        $payload = json_encode($state);
                        if ($payload !== false) {
                            echo "event: queue-update\n";
                            echo 'data: ' . $payload . "\n\n";
                            @ob_flush();
                            flush();
                            try { Log::info('SSE: sent queue-update', ['initial' => true, 'hash' => md5($payload), 'length' => strlen($payload)]); } catch (\Throwable $e) {}
                            $lastState = $payload;
                            $lastCache = Cache::get('antrian_last_update', $lastCache);
                        }
                    } catch (\Throwable $e) {
                        try { Log::error('SSE: buildQueueState failed (initial)', ['message' => $e->getMessage()]); } catch (\Throwable $_) {}
                        $fallback = json_encode(['antrians' => [], 'called' => null, 'error' => true]);
                        echo "event: queue-update\n";
                        echo 'data: ' . $fallback . "\n\n";
                        @ob_flush();
                        flush();
                        $lastState = $fallback;
                    }
                } else {
                    // Check cache flag to avoid hitting DB each loop
                    $currentCache = Cache::get('antrian_last_update', 0);
                    if ($currentCache !== $lastCache) {
                        try {
                            $state = $this->buildQueueState();
                            $payload = json_encode($state);
                            if ($payload !== false && $payload !== $lastState) {
                                echo "event: queue-update\n";
                                echo 'data: ' . $payload . "\n\n";
                                @ob_flush();
                                flush();
                                try { Log::info('SSE: sent queue-update', ['hash' => md5($payload), 'length' => strlen($payload), 'cache' => $currentCache]); } catch (\Throwable $e) {}
                                $lastState = $payload;
                            }
                        } catch (\Throwable $e) {
                            try { Log::error('SSE: buildQueueState failed', ['message' => $e->getMessage()]); } catch (\Throwable $_) {}
                            $fallback = json_encode(['antrians' => [], 'called' => null, 'error' => true]);
                            if ($fallback !== $lastState) {
                                echo "event: queue-update\n";
                                echo 'data: ' . $fallback . "\n\n";
                                @ob_flush();
                                flush();
                                $lastState = $fallback;
                            }
                        }
                        $lastCache = $currentCache;
                    }
                }

                if ((time() - $lastPingAt) >= 30) {
                    echo ": ping\n\n";
                    @ob_flush();
                    flush();
                    try { Log::debug('SSE: ping'); } catch (\Throwable $e) {}
                    $lastPingAt = time();
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
