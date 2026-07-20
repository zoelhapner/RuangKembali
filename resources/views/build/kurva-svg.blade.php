<svg
    xmlns="http://www.w3.org/2000/svg"
    width="100%"
    height="100%"
    viewBox="0 0 {{ $svgWidth }} {{ $svgHeight }}"
    preserveAspectRatio="none">

@php
$countWeek = max(count($weeks),1);
@endphp

@for($i=0;$i<=10;$i++)

    @php
        $y = $paddingTop + ($chartHeight/10) * $i;
    @endphp

    <line
        x1="0"
        y1="{{ $y }}"
        x2="{{ $svgWidth }}"
        y2="{{ $y }}"
        stroke="#dddddd"
        stroke-width="1"
    />

@endfor

@for($i=0;$i<$countWeek;$i++)

    @php
        $x = $paddingLeft + ($i * $stepX);
    @endphp

    <line
        x1="{{ $x }}"
        y1="0"
        x2="{{ $x }}"
        y2="{{ $svgHeight }}"
        stroke="#dddddd"
        stroke-width="1"
    />

@endfor
{{-- ================= LEGEND ================= --}}
@php
    $legendY = 12;

    $boxW = 18;
    $boxH = 10;

    $text1 = 55; // "Realisasi (%)"
    $text2 = 50; // "Rencana (%)"

    $gap = 28;

    $item1 = $boxW + 6 + $text1;
    $item2 = $boxW + 6 + $text2;

    $total = $item1 + $gap + $item2;

    $startX = ($svgWidth - $total) / 2;

    $legend1X = $startX;
    $legend2X = $startX + $item1 + $gap;
@endphp

{{-- ================= REALISASI ================= --}}
<rect
    x="{{ $legend1X }}"
    y="{{ $legendY }}"
    width="{{ $boxW }}"
    height="{{ $boxH }}"
    fill="rgba(54,162,235,.2)"
    stroke="rgb(54,162,235)"
    stroke-width="2"
/>

<text
    x="{{ $legend1X + $boxW + 6 }}"
    y="{{ $legendY + 8 }}"
    font-size="10"
    fill="#666">
    Realisasi (%)
</text>

{{-- ================= RENCANA ================= --}}
<rect
    x="{{ $legend2X }}"
    y="{{ $legendY }}"
    width="{{ $boxW }}"
    height="{{ $boxH }}"
    fill="rgba(255,99,132,.2)"
    stroke="rgb(255,99,132)"
    stroke-width="2"
/>

<text
    x="{{ $legend2X + $boxW + 6 }}"
    y="{{ $legendY + 8 }}"
    font-size="10"
    fill="#666">
    Rencana (%)
</text>
{{-- ========================================== --}}
<polyline
fill="none"
stroke="rgb(255,99,132)"
stroke-width="3"
points="{{ $svgPlan }}"
/>

<polyline
    fill="none"
    stroke="rgb(54,162,235)"
    stroke-width="3"
    points="{{ $svgReal }}"
/>

@foreach($plan as $i => $value)

    @php

        $x = $paddingLeft + ($i * $stepX);

        $y = $paddingTop + $chartHeight - (($value / $safeMax) * $chartHeight);

    @endphp

    <circle
        cx="{{ $x }}"
        cy="{{ $y }}"
        r="3"
        fill="rgb(255,99,132)"
    />

    <text
        x="{{ $x }}"
        y="{{ $y - 10 }}"
        font-size="8"
        text-anchor="middle"
        fill="#0000">

        {{ round($value,1) }}

    </text>

@endforeach

@foreach($realisasi as $i => $value)
    @if(($i + 1) > $weekNow)
        @break
    @endif

    @php
        $x = $paddingLeft + ($i * $stepX);
        $y = $paddingTop + $chartHeight - (($value / $safeMax) * $chartHeight);
    @endphp

    <circle
        cx="{{ $x }}"
        cy="{{ $y }}"
        r="4"
        fill="rgb(54,162,235)"
    />

    <text
        x="{{ $x }}"
        y="{{ $y + 14 }}"
        font-size="8"
        fill="#0000">

        {{ round($value,1) }}

    </text>

@endforeach
<line
    x1="{{ $paddingLeft }}"
    y1="{{ $paddingTop + $chartHeight }}"
    x2="{{ $paddingLeft + ($countWeek - 1) * $stepX }}"
    y2="{{ $paddingTop + $chartHeight }}"
    stroke="#000"
    stroke-width="1"
/>
@foreach($weeks as $i => $w)

    @php
        $x = $paddingLeft + ($i * $stepX);
        $axisY = $paddingTop + $chartHeight;
    @endphp

    <line
        x1="{{ $x }}"
        y1="{{ $axisY }}"
        x2="{{ $x }}"
        y2="{{ $axisY + 5 }}"
        stroke="#000"
        stroke-width="1"
    />

    <text
        x="{{ $x }}"
        y="{{ $axisY + 18 }}"
        font-size="8"
        text-anchor="middle"
        fill="#000">

        M{{ $w['week_no'] }}

    </text>

@endforeach
</svg>