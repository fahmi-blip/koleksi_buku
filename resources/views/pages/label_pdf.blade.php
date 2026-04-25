<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    

    @page {
        size: A4 portrait;
        margin: 4mm 3mm;
    }
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        font-family: Poppins, Helvetica, sans-serif;
        font-size: 7pt;
    }
    
    .page-wrap {
        page-break-after: always;
    }
    
    table.label-sheet {
        width: 100%;
        border-collapse: separate;
        border-spacing: 4mm 2mm;   
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
    
     table.label-sheet td.label-cell {
        border: 0.3pt solid #cccccc;
        border-radius: 1mm;
    }
    
     table.label-sheet td.empty {
        color: #333;
        border: 0.3pt dashed #fffff;
        background: #fafafa;
    } 

    .label-id {
        font-size: 6pt;
        color: #000;
        font-weight: regular;
        margin-bottom: 1mm;
    }

    .label-name {
        font-size: 8pt;
        font-weight: bold;
        color: #000;
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

    .label-barcode {
        margin-top: 1mm;
    }

    .label-barcode img {
        width: 80%;
        height: auto;
        display: block;
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
                <div class="label-id">{{ $item['barang']->id_barang }}</div>
                <div class="label-name">{{ $item['barang']->nama }}</div>
                <div class="label-barcode"><img src="{{ $item['barcodeSvg'] }}" alt="barcode"></div>
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
