<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Pesanan;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap payment token for embedded or redirect payment
     */
    public function generateSnapToken(Pesanan $pesanan): string
    {
        try {
            $pesanan->load(['details', 'user']);

            $transaction_details = [
                'order_id' => $pesanan->order_code,
                'gross_amount' => (int) $pesanan->total,
            ];

            $customer_details = [
                'first_name' => $pesanan->nama,
                'email' => $pesanan->user->email,
            ];

            $items = [];
            foreach ($pesanan->details as $detail) {
                $items[] = [
                    'id' => $detail->idmenu,
                    'price' => (int) $detail->harga,
                    'quantity' => (int) $detail->jumlah,
                    'name' => $detail->menu->nama_menu,
                ];
            }

            $payload = [
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
                'item_details' => $items,
                'callbacks' => [
                    'finish' => route('kantin.payment.show', $pesanan->order_code),
                    'unfinish' => route('kantin.payment.show', $pesanan->order_code),
                    'error' => route('kantin.payment.show', $pesanan->order_code),
                ],
            ];

            return Snap::getSnapToken($payload);
        } catch (Exception $e) {
            throw new Exception('Gagal generate Midtrans snap token: ' . $e->getMessage());
        }
    }

    /**
     * Get Snap Redirect URL for external payment
     */
    public function getSnapRedirectUrl(Pesanan $pesanan): string
    {
        $pesanan->load(['details.menu', 'user']);

        $transaction_details = [
            'order_id' => $pesanan->order_code,
            'gross_amount' => (int) $pesanan->total,
        ];

        $customer_details = [
            'first_name' => $pesanan->nama,
            'email' => $pesanan->user->email,
        ];

        $items = [];
        foreach ($pesanan->details as $detail) {
            $items[] = [
                'id' => $detail->idmenu,
                'price' => (int) $detail->harga,
                'quantity' => (int) $detail->jumlah,
                'name' => $detail->menu->nama_menu,
            ];
        }

        $payload = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $items,
            'callbacks' => [
                'finish' => route('kantin.payment.show', $pesanan->order_code),
                'unfinish' => route('kantin.payment.show', $pesanan->order_code),
                'error' => route('kantin.payment.show', $pesanan->order_code),
            ],
        ];

        $transaction = Snap::createTransaction($payload);

        return (string) ($transaction->redirect_url ?? '');
    }

    /**
     * Check transaction status from Midtrans
     */
    public function checkStatus(string $order_code): array
    {
        try {
            $response = Transaction::status($order_code);
            $status = is_array($response)
                ? ($response['transaction_status'] ?? null)
                : ($response->transaction_status ?? null);

            return [
                'success' => true,
                'status' => $status,
                'data' => $response,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate Midtrans callback signature
     */
    public function validateCallback(array $data): bool
    {
        $signature_key = $data['signature_key'] ?? null;
        $order_id = $data['order_id'] ?? '';
        $status_code = $data['status_code'] ?? '';
        $gross_amount = $data['gross_amount'] ?? '';

        $server_key = config('midtrans.server_key');
        $hash = hash('sha512', $order_id . $status_code . $gross_amount . $server_key);

        return $hash === $signature_key;
    }

    /**
     * Handle Midtrans callback and update order status
     */
    public function handleCallback(array $data): array
    {
        if (!$this->validateCallback($data)) {
            return [
                'success' => false,
                'message' => 'Invalid callback signature',
            ];
        }

        $order_code = $data['order_id'] ?? null;
        $transaction_status = $data['transaction_status'] ?? null;

        if (!$order_code) {
            return [
                'success' => false,
                'message' => 'Missing order_id',
            ];
        }

        $pesanan = Pesanan::where('order_code', $order_code)->first();

        if (!$pesanan) {
            return [
                'success' => false,
                'message' => 'Order not found',
            ];
        }

        $status_mapping = [
            'capture' => 1,
            'settlement' => 1,
            'pending' => 0,
            'deny' => 0,
            'cancel' => 0,
            'expire' => 0,
            'failure' => 0,
        ];

        $new_status = $status_mapping[$transaction_status] ?? 0;

        $isPaid = $new_status === 1;

        $pesanan->update([
            'status_bayar' => $new_status,
            'paid_at' => $isPaid ? ($pesanan->paid_at ?? now()) : null,
            'midtrans_status' => $transaction_status,
            'gateway_payload' => array_merge($pesanan->gateway_payload ?? [], [
                'status' => $isPaid ? 'paid' : 'pending',
                'midtrans_status' => $transaction_status,
                'confirmed_at' => $isPaid ? now()->toDateTimeString() : null,
                'midtrans_data' => $data,
            ]),
        ]);

        return [
            'success' => true,
            'message' => 'Status updated',
            'status_bayar' => $new_status,
            'midtrans_status' => $transaction_status,
        ];
    }
}
