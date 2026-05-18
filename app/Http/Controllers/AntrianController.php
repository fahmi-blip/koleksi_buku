<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AntrianController extends Controller
{
    private const CACHE_SNAPSHOT_KEY = 'antrian.queue.snapshot';
    private const CACHE_VERSION_KEY = 'antrian.queue.version';

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

    private function cacheQueueState(?array $state = null): array
    {
        $state = $state ?? $this->buildQueueState();

        Cache::forever(self::CACHE_SNAPSHOT_KEY, $state);
        Cache::forever(self::CACHE_VERSION_KEY, (string) microtime(true));

        return $state;
    }

    private function getCachedQueueState(): array
    {
        $state = Cache::get(self::CACHE_SNAPSHOT_KEY);

        if (is_array($state) && array_key_exists('antrians', $state) && array_key_exists('called', $state)) {
            return $state;
        }

        return $this->cacheQueueState();
    }

    private function getQueueVersion(): string
    {
        $version = Cache::get(self::CACHE_VERSION_KEY);

        if (is_string($version) && $version !== '') {
            return $version;
        }

        $this->cacheQueueState();

        $version = Cache::get(self::CACHE_VERSION_KEY);

        return is_string($version) && $version !== '' ? $version : (string) microtime(true);
    }

    private function getQueuePayload(bool $refresh = false): array
    {
        $state = $refresh ? $this->cacheQueueState() : $this->getCachedQueueState();

        return [
            'antrians' => $state['antrians'],
            'called' => $state['called'],
            'version' => $this->getQueueVersion(),
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
        $usePolling = PHP_SAPI === 'cli-server';
        $state = $this->getQueuePayload();
        $antrians = $state['antrians'];
        $called = $state['called'];
        $queueVersion = $state['version'];

        return view('antrian.guest', compact('usePolling', 'queueVersion', 'antrians', 'called'));
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

        $this->cacheQueueState();

        try { Log::info('Antrian store: cache snapshot updated', ['version' => Cache::get(self::CACHE_VERSION_KEY)]); } catch (\Throwable $_) {}

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
        $state = $this->getQueuePayload();
        $antrians = $state['antrians'];
        $called = $state['called'];
        $queueVersion = $state['version'];
        $usePolling = PHP_SAPI === 'cli-server';

        return view('antrian.admin', compact('antrians', 'called', 'usePolling', 'queueVersion'));
    }

    public function snapshot()
    {
        $state = $this->getQueuePayload();

        return response()->json([
            'success' => true,
            'antrians' => $state['antrians'],
            'called' => $state['called'],
            'version' => $state['version'],
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
        $state = $this->cacheQueueState();

        return response()->json([
            'success' => true,
            'called' => $called,
            'antrians' => $state['antrians'],
            'version' => Cache::get(self::CACHE_VERSION_KEY),
        ]);
    }

    public function finish(Request $request, Antrian $antrian)
    {
        DB::table('antrians')->where('id', $antrian->id)->update([
            'status' => 'selesai',
            'updated_at' => now()->toDateTimeString(),
        ]);
        try { Log::info('Antrian finished', ['id' => $antrian->id]); } catch (\Throwable $e) {}
        $state = $this->cacheQueueState();
        try { Log::info('Antrian finish: cache snapshot updated', ['version' => Cache::get(self::CACHE_VERSION_KEY)]); } catch (\Throwable $_) {}

        return response()->json([
            'success' => true,
            'antrians' => $state['antrians'],
            'called' => $state['called'],
            'version' => Cache::get(self::CACHE_VERSION_KEY),
        ]);
    }

    public function late(Request $request, Antrian $antrian)
    {
        DB::table('antrians')->where('id', $antrian->id)->update([
            'status' => 'terlambat',
            'updated_at' => now()->toDateTimeString(),
        ]);
        try { Log::info('Antrian late', ['id' => $antrian->id]); } catch (\Throwable $e) {}
        $state = $this->cacheQueueState();
        try { Log::info('Antrian late: cache snapshot updated', ['version' => Cache::get(self::CACHE_VERSION_KEY)]); } catch (\Throwable $_) {}

        return response()->json([
            'success' => true,
            'antrians' => $state['antrians'],
            'called' => $state['called'],
            'version' => Cache::get(self::CACHE_VERSION_KEY),
        ]);
    }

    public function sse()
    {
        // Sesuaikan session file lock agar tab lain tidak hang ketika request POST/GET lain dilakukan.
        if (request()->hasSession()) {
            request()->session()->save();
        }

        return response()->stream(function () {
            @set_time_limit(0);
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', '0');

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            ignore_user_abort(true);

            $lastVersion = null;
            $lastPingAt = 0;

            $initialState = $this->getQueuePayload();
            $initialPayload = json_encode([
                'antrians' => $initialState['antrians'],
                'called' => $initialState['called'],
                'version' => $initialState['version'],
            ]);

            if ($initialPayload !== false) {
                echo "event: queue-update\n";
                echo 'data: ' . $initialPayload . "\n\n";
                @ob_flush();
                flush();
                $lastVersion = $initialState['version'];
            }

            while (!connection_aborted()) {
                try {
                    $version = $this->getQueueVersion();

                    if ($version !== $lastVersion) {
                        $state = $this->getQueuePayload();
                        $payload = json_encode([
                            'antrians' => $state['antrians'],
                            'called' => $state['called'],
                            'version' => $state['version'],
                        ]);

                        if ($payload !== false) {
                            echo "event: queue-update\n";
                            echo 'data: ' . $payload . "\n\n";
                            @ob_flush();
                            flush();
                            try { Log::info('SSE: sent queue-update', ['version' => $version, 'length' => strlen($payload)]); } catch (\Throwable $e) {}
                            $lastVersion = $version;
                        }
                    }
                } catch (\Throwable $e) {
                    try { Log::error('SSE: buildQueueState failed', ['message' => $e->getMessage()]); } catch (\Throwable $_) {}
                    $fallback = json_encode(['antrians' => [], 'called' => null, 'error' => true]);
                    if ($fallback !== false) {
                        if ($lastVersion !== 'error') {
                            echo "event: queue-update\n";
                            echo 'data: ' . $fallback . "\n\n";
                            @ob_flush();
                            flush();
                            $lastVersion = 'error';
                        }
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
    public function papan()
{
        $state = $this->getQueuePayload();
        $usePolling = PHP_SAPI === 'cli-server';
    return view('antrian.papan', [
        'antrians' => $state['antrians'],
        'called'   => $state['called'],
            'usePolling' => $usePolling,
            'queueVersion' => $state['version'],
    ]);
}
}
