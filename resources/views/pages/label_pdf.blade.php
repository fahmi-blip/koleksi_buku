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
    
    col.label-col { width: 64mm; }
    
    table.label-sheet td {
        height: 32mm;           
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
        font-size: 8pt;
        color: #000;
        font-weight: bold;
        margin-bottom: 1mm;
        text-transform: uppercase;
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
</style>
</head>
<body>

@foreach($pages as $pageIndex => $slots)
<div class="{{ !$loop->last ? 'page-wrap' : '' }}">
<table class="label-sheet">
    <colgroup>
        @for($c = 0; $c < 3; $c++)
            <col class="label-col">
        @endfor
    </colgroup>

    @for($r = 0; $r < 4; $r++)
    <tr>
        @for($c = 0; $c < 3; $c++)
            @php $idx = $r * 3 + $c; $item = $slots[$idx] ?? null; @endphp
            @if($item)
            <td class="label-cell">
                <div class="label-id">Feri Kurniawan, S. H.</div>
                <div class="label-name">Batuan</div>
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
