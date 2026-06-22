@php
/**
 * PDF Corporativo — Usuarios
 *
 * Arquitectura basada en código fuente dompdf 3.1.5:
 * - src/FrameReflower/Page.php línea 106: fixed children se extraen del body,
 *   se deep_copy() y se prepend_child() en CADA página nueva.
 * - src/Positioner/Fixed.php: top/left se suman al margin_top/margin_left del @page.
 *   Con @page margin-top:0, top:0 = borde superior del papel físico.
 * - Solución: @page sin margen superior/inferior; el contenido tiene padding propio;
 *   el header/footer fixed usan top:0 y bottom:0 absolutos al papel.
 * - table-layout:fixed: columnas predecibles (CSS 2.1 compliant).
 * - DejaVu Sans: única fuente con UTF-8 completo pre-cargada en dompdf.
 */

$hex    = ltrim($primaryColor, '#');
$rr     = hexdec(substr($hex, 0, 2));
$gg     = hexdec(substr($hex, 2, 2));
$bb     = hexdec(substr($hex, 4, 2));
$darker = sprintf('#%02x%02x%02x', max(0,$rr-60),   max(0,$gg-60),   max(0,$bb-60));
$pale   = sprintf('#%02x%02x%02x', min(255,$rr+195), min(255,$gg+195), min(255,$bb+195));
$xlight = sprintf('#%02x%02x%02x', min(255,$rr+230), min(255,$gg+230), min(255,$bb+230));

$isL = ($orientation === 'landscape');
$pw  = $isL ? '297mm' : '210mm';
$ph  = $isL ? '210mm' : '297mm';

// Stats
$cTotal  = $users->count();
$cAct    = $users->filter(fn($u) => $u->status?->value === 'active')->count();
$cIna    = $users->filter(fn($u) => $u->status?->value === 'inactive')->count();
$cBan    = $users->filter(fn($u) => $u->status?->value === 'banned')->count();
$cVer    = $users->filter(fn($u) => $u->email_verified_at)->count();
$byRole  = $users->groupBy(fn($u) => $u->roles->first()?->name ?? 'Sin rol');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
/**
 * @page sin margen top/bottom = header/footer fixed
 * usan todo el espacio del papel (top:0, bottom:0).
 * El body tiene padding-top/bottom para no quedar bajo el header/footer.
 */
@page {
  margin: 0;
  size: {{ $pw }} {{ $ph }};
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: DejaVu Sans, sans-serif;
  font-size: {{ $isL ? '7.5pt' : '8pt' }};
  color: #1e293b;
  background: #ffffff;
  line-height: 1.4;
  /* Reservar espacio para header y footer fijos */
  padding-top:    32mm;
  padding-bottom: 14mm;
  padding-left:   16mm;
  padding-right:  16mm;
}

/* ══════════════════════════════════════════════════
   HEADER FIJO — se repite en cada página nueva
   Fuente: Page.php prepend_child(deep_copy(fixed))
   top:0 = borde superior del papel físico
   ══════════════════════════════════════════════════ */
#hdr {
  position: fixed;
  top:   0;
  left:  0;
  right: 0;
  width: 100%;
  height: 30mm;
}

/* Banda principal de color */
#hdr .band {
  background: {{ $primaryColor }};
  width: 100%;
  height: 22mm;
}

/* Tabla interna del header — 3 columnas */
#hdr .band-tbl {
  width: 100%;
  height: 22mm;
  border-collapse: collapse;
  padding: 0 16mm;
}
#hdr .col-logo {
  width: 18mm;
  vertical-align: middle;
  padding-right: 4mm;
}
#hdr .logo-img {
  display: block;
  width: 13mm;
  height: 13mm;
  object-fit: contain;
  background: #fff;
  border-radius: 3px;
  padding: 1.5px;
}
#hdr .logo-placeholder {
  width: 13mm; height: 13mm;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 3px;
  display: block;
}
#hdr .col-info { vertical-align: middle; }
#hdr .eyebrow {
  font-size: 5.5pt;
  color: {{ $pale }};
  text-transform: uppercase;
  letter-spacing: 2px;
  font-weight: bold;
  margin-bottom: 2px;
}
#hdr .title {
  font-size: 14pt;
  font-weight: bold;
  color: #ffffff;
  line-height: 1;
}
#hdr .org {
  font-size: 7.5pt;
  color: {{ $pale }};
  margin-top: 2px;
}
#hdr .col-meta {
  width: 55mm;
  vertical-align: middle;
  text-align: right;
}
#hdr .mline {
  font-size: 6.5pt;
  color: {{ $pale }};
  line-height: 1.8;
}
#hdr .mline b { color: #ffffff; }

/* Franja acento */
#hdr .accent {
  height: 2.5mm;
  background: {{ $darker }};
  width: 100%;
}

/* Barra corporativa */
#hdr .corp {
  height: 5.5mm;
  background: {{ $xlight }};
  border-bottom: 1px solid {{ $pale }};
  width: 100%;
  padding: 0 16mm;
}
#hdr .corp-tbl {
  width: 100%;
  height: 5.5mm;
  border-collapse: collapse;
}
#hdr .corp-td {
  font-size: 6pt;
  color: #475569;
  padding-right: 8mm;
  vertical-align: middle;
  white-space: nowrap;
}
#hdr .dot { color: {{ $primaryColor }}; margin-right: 1.5px; }

/* ══════════════════════════════════════════════════
   FOOTER FIJO
   ══════════════════════════════════════════════════ */
#ftr {
  position: fixed;
  bottom: 0;
  left:   0;
  right:  0;
  width:  100%;
  height: 13mm;
  background: #ffffff;
}
#ftr .fline {
  height: 1.5px;
  background: {{ $primaryColor }};
  width: 100%;
}
#ftr .fbody {
  width: 100%;
  border-collapse: collapse;
  padding: 0 16mm;
}
#ftr .fl {
  font-size: 6pt;
  color: #94a3b8;
  vertical-align: top;
  padding-top: 2mm;
}
#ftr .fl b { color: {{ $primaryColor }}; font-size: 6.5pt; }
#ftr .fr {
  font-size: 6pt;
  color: #94a3b8;
  text-align: right;
  vertical-align: top;
  padding-top: 2mm;
}
/* Numeración de páginas — counter nativo dompdf */
.pagenum:before   { content: counter(page); }
.pagetotal:before { content: counter(pages); }

/* ══════════════════════════════════════════════════
   CONTENIDO
   ══════════════════════════════════════════════════ */
.sec {
  font-size: 6pt;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: {{ $primaryColor }};
  border-bottom: 1.5px solid {{ $primaryColor }};
  padding-bottom: 2px;
  margin-bottom: 7px;
}

.filter-box {
  background: {{ $xlight }};
  border-left: 3px solid {{ $primaryColor }};
  padding: 4px 8px;
  margin-bottom: 8px;
  font-size: 7pt;
  color: #475569;
}
.filter-box b { color: #0f172a; }

/* Stats */
.stats {
  width: 100%;
  border-collapse: separate;
  border-spacing: 4px 0;
  margin-bottom: 10px;
  table-layout: fixed;
}
.stats td {
  border: 1px solid #e2e8f0;
  border-top: 3px solid {{ $primaryColor }};
  padding: 7px 3px 6px;
  text-align: center;
  background: #fff;
}
.stats td.hi {
  background: {{ $xlight }};
  border-top-color: {{ $darker }};
}
.sn { font-size: 13pt; font-weight: bold; line-height: 1; }
.sl { font-size: 5pt; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.3px; }
.cp { color: {{ $primaryColor }}; }
.cg { color: #059669; }
.cy { color: #d97706; }
.cr { color: #dc2626; }
.cb { color: #2563eb; }

/* Tabla datos */
.dt {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  margin-bottom: 6px;
}
.dt thead th {
  background: {{ $primaryColor }};
  color: #fff;
  font-size: {{ $isL ? '6pt' : '6.5pt' }};
  font-weight: bold;
  padding: {{ $isL ? '4px 3px' : '4.5px 4px' }};
  text-align: left;
  border-right: 1px solid {{ $darker }};
  overflow: hidden;
}
.dt thead th:last-child { border-right: none; }
.dt thead th.tc { text-align: center; }
.dt tbody tr.r0 td { background: #ffffff; }
.dt tbody tr.r1 td { background: {{ $xlight }}; }
.dt tbody td {
  font-size: {{ $isL ? '6pt' : '6.5pt' }};
  padding: {{ $isL ? '3px 3px' : '4px 4px' }};
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #f1f5f9;
  vertical-align: middle;
  overflow: hidden;
}
.dt tbody td:last-child { border-right: none; }
.dt tbody tr:last-child td { border-bottom: 2px solid {{ $pale }}; }
.tc { text-align: center; }
.num { text-align: center; color: #cbd5e1; font-size: 5.5pt; }
.un  { font-weight: bold; color: #0f172a; }
.ue  { font-size: 5.5pt; color: #94a3b8; margin-top: 1px; }

.pill {
  display: inline-block;
  font-size: 5.5pt; font-weight: bold;
  color: {{ $primaryColor }};
  border: 1px solid {{ $primaryColor }};
  padding: 1px 3px; border-radius: 2px;
}

.b { display: inline-block; padding: 1.5px 4px; border-radius: 2px; font-size: 5.5pt; font-weight: bold; text-transform: uppercase; }
.ba { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.bi { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
.bb { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.bv { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
.bn { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

</style>
</head>
<body>

{{-- ══ HEADER FIJO ══════════════════════════════════════
     Hijo directo de body = dompdf lo trata como fixed
     y lo copia en cada página automáticamente
══════════════════════════════════════════════════════ --}}
<div id="hdr">
  <div class="band">
    <table class="band-tbl">
      <tr>
        <td class="col-logo">
          @if($logoBase64)
            <img class="logo-img" src="data:{{ $logoMime }};base64,{{ $logoBase64 }}" alt=""/>
          @else
            <div class="logo-placeholder"></div>
          @endif
        </td>
        <td class="col-info">
          <div class="eyebrow">Reporte Oficial &nbsp;&bull;&nbsp; Gestión de Personal</div>
          <div class="title">{{ strtoupper($siteName) }}</div>
          <div class="org">
            {{ $companyName }}
            @if($companyRuc) &nbsp;&bull;&nbsp; RUC {{ $companyRuc }} @endif
          </div>
        </td>
        <td class="col-meta">
          <div class="mline">Fecha:&nbsp;<b>{{ now()->format('d/m/Y') }}</b></div>
          <div class="mline">Hora:&nbsp;<b>{{ now()->format('H:i') }}</b></div>
          <div class="mline">Por:&nbsp;<b>{{ auth()->user()?->name ?? 'Sistema' }}</b></div>
          <div class="mline">Registros:&nbsp;<b>{{ $cTotal }}</b></div>
          <div class="mline">Papel:&nbsp;<b>A4 {{ $isL ? 'Horizontal' : 'Vertical' }}</b></div>
        </td>
      </tr>
    </table>
  </div>
  <div class="accent"></div>
  <div class="corp">
    <table class="corp-tbl">
      <tr>
        @if($companyAddress) <td class="corp-td"><span class="dot">&#9679;</span>{{ $companyAddress }}</td> @endif
        @if($companyPhone)   <td class="corp-td"><span class="dot">&#9742;</span>{{ $companyPhone }}</td>   @endif
        @if($companyEmail)   <td class="corp-td"><span class="dot">&#9993;</span>{{ $companyEmail }}</td>   @endif
        @if($companyWebsite) <td class="corp-td"><span class="dot">&#9670;</span>{{ $companyWebsite }}</td> @endif
      </tr>
    </table>
  </div>
</div>

{{-- ══ FOOTER FIJO ══════════════════════════════════════ --}}
<div id="ftr">
  <div class="fline"></div>
  <table class="fbody">
    <tr>
      <td class="fl">
        <b>{{ $siteName }}</b> &mdash; {{ $companyName }}
        @if($companyRuc) &mdash; RUC {{ $companyRuc }} @endif
        <br>Documento institucional confidencial.
      </td>
      <td class="fr">
        {{ now()->format('d/m/Y H:i') }}
        &bull; Pág.&nbsp;<span class="pagenum"></span>&nbsp;/&nbsp;<span class="pagetotal"></span>
      </td>
    </tr>
  </table>
</div>

{{-- ══ CONTENIDO ══════════════════════════════════════ --}}
@if($filters)
<div class="filter-box">Filtros: <b>{{ $filters }}</b></div>
@endif

<div class="sec">Resumen estadístico</div>
<table class="stats">
  <tr>
    <td class="hi"><div class="sn cp">{{ $cTotal }}</div><div class="sl">Total</div></td>
    <td><div class="sn cg">{{ $cAct }}</div><div class="sl">Activos</div></td>
    <td><div class="sn cy">{{ $cIna }}</div><div class="sl">Inactivos</div></td>
    <td><div class="sn cr">{{ $cBan }}</div><div class="sl">Bloqueados</div></td>
    <td><div class="sn cb">{{ $cVer }}</div><div class="sl">Verificados</div></td>
    @foreach($byRole as $rol => $grp)
    <td><div class="sn cp">{{ $grp->count() }}</div><div class="sl">{{ $rol }}</div></td>
    @endforeach
  </tr>
</table>

<div class="sec" style="margin-top:8px">
  Listado de usuarios &mdash; {{ now()->format('d/m/Y H:i') }}
</div>

<table class="dt">
  <thead>
    <tr>
      <th class="tc" style="width:3%">#</th>
      <th style="width:{{ $isL?'13%':'19%' }}">Nombre / Email</th>
      @if($isL)<th style="width:9%">Usuario</th>@endif
      <th class="tc" style="width:{{ $isL?'7%':'8%' }}">Rol</th>
      <th style="width:{{ $isL?'13%':'17%' }}">Cargo</th>
      <th style="width:{{ $isL?'11%':'14%' }}">Área</th>
      @if($isL)
        <th class="tc" style="width:7%">DNI</th>
        <th style="width:8%">Teléfono</th>
      @endif
      <th class="tc" style="width:{{ $isL?'8%':'9%' }}">Estado</th>
      <th class="tc" style="width:{{ $isL?'5%':'6%' }}">Email</th>
      <th class="tc" style="width:{{ $isL?'9%':'12%' }}">Últ. acceso</th>
      <th class="tc" style="width:{{ $isL?'7%':'12%' }}">Registro</th>
    </tr>
  </thead>
  <tbody>
    @foreach($users as $i => $u)
    @php $sc = match($u->status?->value){'active'=>'ba','inactive'=>'bi','banned'=>'bb',default=>'bn'}; @endphp
    <tr class="r{{ $i%2 }}">
      <td class="num">{{ $i+1 }}</td>
      <td><div class="un">{{ $u->name }}</div><div class="ue">{{ $u->email }}</div></td>
      @if($isL)<td style="color:#64748b">{{ $u->username ?? '—' }}</td>@endif
      <td class="tc"><span class="pill">{{ $u->roles->first()?->name ?? '—' }}</span></td>
      <td>{{ $u->perfil?->cargo ?? '—' }}</td>
      <td style="color:#64748b">{{ $u->perfil?->area ?? '—' }}</td>
      @if($isL)
        <td class="tc">{{ $u->perfil?->dni ?? '—' }}</td>
        <td>{{ $u->perfil?->celular ?? $u->phone ?? '—' }}</td>
      @endif
      <td class="tc"><span class="b {{ $sc }}">{{ $u->status?->label() ?? '—' }}</span></td>
      <td class="tc"><span class="b {{ $u->email_verified_at ? 'bv' : 'bn' }}">{{ $u->email_verified_at ? 'Sí' : 'No' }}</span></td>
      <td class="tc" style="color:#64748b">
        {{ $u->last_login_at?->format('d/m/Y') ?? 'Nunca' }}
        @if($u->last_login_at)<br><span style="color:#94a3b8;font-size:5.5pt">{{ $u->last_login_at->format('H:i') }}</span>@endif
      </td>
      <td class="tc" style="color:#94a3b8">{{ $u->created_at->format('d/m/Y') }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

</body>
</html>
