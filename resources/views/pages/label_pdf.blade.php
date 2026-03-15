<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    

    @page {
        size: A4 portrait;
        margin: 10.65mm 10.65mm;
    }
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 7pt;
    }
    
    .page-wrap {
        page-break-after: always;
    }
    
    table.label-sheet {
        width: 100%;
        border-collapse: separate;
        border-spacing: 2mm 2mm;   
        table-layout: fixed;
        margin: 0 auto;
    }
    
    col.label-col { width: 38mm; }
    
    table.label-sheet td {
        height: 18mm;           
        vertical-align: middle;
        text-align: center;
        overflow: hidden;
    }
    
     /* table.label-sheet td.label-cell {
        border: 0.3pt solid #cccccc;
        border-radius: 1mm;
    }
    
     table.label-sheet td.empty {
        color: #333;
        border: 0.3pt dashed #fffff;
        background: #fafafa;
    }  */

    .label-id {
        font-size: 5.5pt;
        color: #555;
        letter-spacing: 0.5px;
        margin-bottom: 1mm;
        text-transform: uppercase;
    }

    .label-name {
        font-size: 7pt;
        font-weight: bold;
        color: #222;
        margin-bottom: 1mm;
        line-height: 1.2;
        overflow: hidden;
    }

    .label-price {
        font-size: 9pt;
        font-weight: bold;
        color: #c0392b;
        letter-spacing: 0.3px;
    }

    .label-price-label {
        font-size: 5pt;
        color: #999;
        display: block;
        margin-top: 0.3mm;
    }
</style>
</head>
<body>

@foreach($pages as $pageIndex => $slots)
<div class="{{ !$loop->last ? 'page-wrap' : '' }}">
<table class="label-sheet">
    <colgroup>
        @for($c = 0; $c < 5; $c++)
            <col class="label-col">
        @endfor
    </colgroup>

    @for($r = 0; $r < 8; $r++)
    <tr>
        @for($c = 0; $c < 5; $c++)
            @php $idx = $r * 5 + $c; $item = $slots[$idx] ?? null; @endphp
            @if($item)
            <td class="label-cell">
                <div class="label-id">{{ $item->id_barang }}</div>
                <div class="label-name">{{ $item->nama }}</div>
                <div class="label-price">
                    Rp {{ number_format($item->harga, 0, ',', '.') }}
                    <span class="label-price-label">HARGA</span>
                </div>
            </td>
            @else
            <td class="label-cell empty"></td>
            @endif
        @endfor
    </tr>
    @endfor
</table>
</div>
@endforeach

</body>
</html>
