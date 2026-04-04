@extends('layouts.kantin')

@section('content')
<div style="display:grid;grid-template-columns:1fr 0.9fr;gap:20px;align-items:start;">
    <section style="border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.72);border-radius:24px;padding:22px;">
        <div style="color:#38bdf8;text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;">Pembayaran</div>
        <h2 style="margin:10px 0 8px;">Pesanan {{ $pesanan->order_code }}</h2>
        <p style="margin:0 0 18px;color:#94a3b8;">Customer: {{ $pesanan->nama }} | Vendor: {{ $pesanan->vendor->nama_vendor }}</p>

        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;">
            <div style="padding:16px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(148,163,184,.16);">
                <div style="color:#94a3b8;font-size:.82rem;">Metode bayar</div>
                <strong style="display:block;margin-top:4px;text-transform:uppercase;">{{ str_replace('_', ' ', $pesanan->metode_bayar) }}</strong>
            </div>
            <div style="padding:16px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(148,163,184,.16);">
                <div style="color:#94a3b8;font-size:.82rem;">Status pembayaran</div>
                <strong style="display:block;margin-top:4px;color:{{ (int) $pesanan->status_bayar === 1 ? '#22c55e' : '#f59e0b' }};">{{ (int) $pesanan->status_bayar === 1 ? 'Lunas' : 'Menunggu pembayaran' }}</strong>
            </div>
            <div style="padding:16px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(148,163,184,.16);">
                <div style="color:#94a3b8;font-size:.82rem;">Reference</div>
                <strong style="display:block;margin-top:4px;">{{ $pesanan->payment_reference }}</strong>
            </div>
            <div style="padding:16px;border-radius:18px;background:rgba(255,255,255,.05);border:1px solid rgba(148,163,184,.16);">
                <div style="color:#94a3b8;font-size:.82rem;">Total</div>
                <strong style="display:block;margin-top:4px;color:#22c55e;">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</strong>
            </div>
        </div>

        @if ($paymentMethod === 'midtrans' && $snapToken)
            <button id="pay-button" onclick="snapPay()" style="padding:14px 18px;width:100%;border:none;border-radius:16px;font-weight:700;background:linear-gradient(135deg,#22c55e,#16a34a);color:white;cursor:pointer;margin-top:18px;{{ (int) $pesanan->status_bayar === 1 ? 'display:none;' : '' }}">Lanjutkan ke Pembayaran Midtrans</button>
        @else
            <form method="POST" action="{{ route('kantin.payment.confirm', $pesanan->order_code) }}" style="margin-top:18px;">
                @csrf
                <button type="submit" style="padding:14px 18px;width:100%;border:none;border-radius:16px;font-weight:700;background:linear-gradient(135deg,#22c55e,#16a34a);color:white;cursor:pointer;{{ (int) $pesanan->status_bayar === 1 ? 'display:none;' : '' }}">Konfirmasi Pembayaran</button>
            </form>
        @endif
    </section>

    <aside style="display:grid;gap:20px;">
        <section style="border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.72);border-radius:24px;padding:22px;">
            <h3 style="margin-top:0;">Detail item</h3>
            <div style="display:grid;gap:10px;">
                @foreach ($pesanan->details as $detail)
                    <div style="padding:14px;border-radius:16px;background:rgba(255,255,255,.04);border:1px solid rgba(148,163,184,.12);">
                        <strong style="display:block;">{{ $detail->menu->nama_menu }}</strong>
                        <small style="color:#94a3b8;">{{ $detail->jumlah }} x Rp {{ number_format($detail->harga, 0, ',', '.') }}</small>
                        @if ($detail->catatan)
                            <div style="margin-top:6px;color:#cbd5e1;">{{ $detail->catatan }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section style="border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.72);border-radius:24px;padding:22px;">
            <h3 style="margin-top:0;">Informasi pembayaran</h3>
            @if ($paymentMethod === 'midtrans' && $snapToken)
                <p style="color:#94a3b8;line-height:1.7;">Klik tombol di samping untuk melanjutkan ke gateway pembayaran Midtrans. Pilih metode pembayaran sesuai pilihan Anda (Kartu Kredit, Virtual Account, E-Wallet, dll).</p>
                <div style="padding:16px;border-radius:18px;background:linear-gradient(135deg,rgba(56,189,248,.14),rgba(34,197,94,.12));border:1px dashed rgba(148,163,184,.25);text-align:center;">
                    <div style="font-size:.82rem;color:#cbd5e1;">Status: Menunggu pembayaran via Midtrans</div>
                    <div style="font-size:1.1rem;font-weight:700;margin-top:6px;">{{ $pesanan->payment_reference }}</div>
                </div>
            @else
                <p style="color:#94a3b8;line-height:1.7;">Aplikasi ini menyiapkan alur pembayaran VA/QRIS. Setelah pembayaran diproses, tombol konfirmasi akan mengubah status menjadi Lunas.</p>
                <div style="padding:16px;border-radius:18px;background:linear-gradient(135deg,rgba(56,189,248,.14),rgba(245,158,11,.12));border:1px dashed rgba(148,163,184,.25);text-align:center;">
                    <div style="font-size:.82rem;color:#cbd5e1;">{{ strtoupper(str_replace('_', ' ', $pesanan->metode_bayar)) }}</div>
                    <div style="font-size:1.3rem;font-weight:700;margin-top:6px;">{{ $pesanan->payment_reference }}</div>
                </div>
            @endif
        </section>
    </aside>
</div>

@if ($paymentMethod === 'midtrans' && $snapToken)
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    function snapPay() {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                console.log('Payment success:', result);
                fetch('{{ route("kantin.payment.show", $pesanan->order_code) }}')
                    .then(response => response.text())
                    .then(html => {
                        document.body.innerHTML = html;
                        window.location.href = '{{ route("kantin.payment.show", $pesanan->order_code) }}';
                    });
            },
            onPending: function(result) {
                console.log('Payment pending:', result);
                alert('Menunggu pembayaran Anda');
            },
            onError: function(result) {
                console.log('Payment error:', result);
                alert('Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: function() {
                console.log('Payment widget closed');
                alert('Anda menutup widget pembayaran. Silakan coba lagi jika ingin melakukan pembayaran.');
            }
        });
    }
</script>
@endif

@endsection