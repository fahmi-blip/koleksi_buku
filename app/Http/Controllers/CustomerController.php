<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::latest()->get();
        $mode = $request->query('mode', 'blob');

        if (! in_array($mode, ['blob', 'file'], true)) {
            $mode = 'blob';
        }

        return view('pages.customer.index', compact('customers', 'mode'));
    }

    public function storeBlob(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kodepos_kelurahan' => 'required|string|max:100',
            'photo_data' => 'required|string',
        ]);

        $mimeType = $this->extractMimeFromDataUrl($validated['photo_data']);

        $customer = Customer::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'provinsi' => $validated['provinsi'],
            'kota' => $validated['kota'],
            'kecamatan' => $validated['kecamatan'],
            'kodepos_kelurahan' => $validated['kodepos_kelurahan'],
            // Store data URL string in DB for PostgreSQL-safe blob workflow.
            'foto_blob' => $validated['photo_data'],
            'foto_blob_mime' => $mimeType,
            'metode_foto' => 'blob',
        ]);

        $customer->update([
            'foto_link' => route('customer.photo', $customer->idcustomer),
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan (metode Blob).');
    }

    public function storeFile(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'provinsi' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kodepos_kelurahan' => 'required|string|max:100',
            'photo_data' => 'required|string',
        ]);

        [$mimeType, $binary] = $this->decodeBase64Image($validated['photo_data']);

        $extension = $this->extensionFromMime($mimeType);
        $fileName = 'customer_' . now()->format('Ymd_His_u') . '.' . $extension;
        $relativePath = 'customer/' . $fileName;

        Storage::disk('public')->put($relativePath, $binary);

        $customer = Customer::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'provinsi' => $validated['provinsi'],
            'kota' => $validated['kota'],
            'kecamatan' => $validated['kecamatan'],
            'kodepos_kelurahan' => $validated['kodepos_kelurahan'],
            'foto_path' => $relativePath,
            'metode_foto' => 'file',
        ]);

        $customer->update([
            'foto_link' => route('customer.photo', $customer->idcustomer),
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan (metode File Path).');
    }

    public function photo(Customer $customer)
    {
        if ($customer->metode_foto === 'blob' && $customer->foto_blob) {
            $blobValue = is_resource($customer->foto_blob)
                ? (string) stream_get_contents($customer->foto_blob)
                : (string) $customer->foto_blob;

            if (preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $blobValue)) {
                $mimeType = $this->extractMimeFromDataUrl($blobValue);
                $raw = substr($blobValue, strpos($blobValue, ',') + 1);
                $binary = base64_decode($raw, true);

                if ($binary === false) {
                    abort(404, 'Foto customer tidak valid.');
                }

                return response($binary, Response::HTTP_OK)
                    ->header('Content-Type', $mimeType)
                    ->header('Cache-Control', 'public, max-age=86400');
            }

            return response($blobValue, Response::HTTP_OK)
                ->header('Content-Type', $customer->foto_blob_mime ?? 'image/jpeg')
                ->header('Cache-Control', 'public, max-age=86400');
        }

        if ($customer->metode_foto === 'file' && $customer->foto_path && Storage::disk('public')->exists($customer->foto_path)) {
            return redirect(Storage::url($customer->foto_path));
        }

        abort(404, 'Foto customer tidak ditemukan.');
    }

    private function decodeBase64Image(string $dataUrl): array
    {
        if (! preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $dataUrl, $matches)) {
            throw ValidationException::withMessages([
                'photo_data' => 'Format foto tidak valid. Gunakan PNG/JPG dari kamera.',
            ]);
        }

        $raw = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $binary = base64_decode($raw, true);

        if ($binary === false) {
            throw ValidationException::withMessages([
                'photo_data' => 'Foto gagal diproses. Silakan ambil ulang foto.',
            ]);
        }

        $mime = strtolower($matches[1]) === 'jpg' ? 'image/jpeg' : 'image/' . strtolower($matches[1]);

        return [$mime, $binary];
    }

    private function extensionFromMime(string $mimeType): string
    {
        return match ($mimeType) {
            'image/png' => 'png',
            default => 'jpg',
        };
    }

    private function extractMimeFromDataUrl(string $dataUrl): string
    {
        if (! preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $dataUrl, $matches)) {
            throw ValidationException::withMessages([
                'photo_data' => 'Format foto tidak valid. Gunakan PNG/JPG dari kamera.',
            ]);
        }

        return strtolower($matches[1]) === 'jpg' ? 'image/jpeg' : 'image/' . strtolower($matches[1]);
    }
}