@php
/**
 * PDF Corporativo — Roles y Permisos
 * Misma arquitectura que users-pdf.blade.php (dompdf 3.1.5):
 * @page margin:0 → header/footer fixed a top:0 / bottom:0
 * body padding-top/bottom reserva espacio visual.
 */

$hex    = ltrim($primaryColor, '#');
$rr     = hexdec(substr($hex, 0, 2));
$gg     = hexdec(substr($hex, 2, 2));
$bb     = hexdec(substr($hex, 4, 2));
$darker = sprintf('#%02x%02x%02x', max(0,$rr-60),   max(0,$gg-60),   max(0,$bb-60));
$pale   = sprintf('#%02x%02x%02x', min(255,$rr+195), min(255,$gg+195), min(255,$bb+195));
$xlight = sprintf('#%02x%02x%02x', min(255,$rr+230), min(255,$gg+230), min(255,$bb+230));

$isL   = ($orientation === 'landscape');
$pw    = $isL ? '297mm' : '210mm';
$ph    = $isL ? '210mm' : '297mm';
$cTot  = $roles->count();
$cPerm = $roles->sum(fn($r) => $r->permissions->count());
$cUser = $roles->sum(fn($r) => $r->users->count());
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
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
  padding-top:    32mm;
  padding-bottom: 14mm;
  padding-left:   16mm;
  padding-right:  16mm;
}

/* ── HEADER FIJO ── */
#hdr {
  position: fixed;
  top:   0;
  left:  0;
  right: 0;
  width: 100%;
  height: 30mm;
}
#hdr .band { background: {{ $primaryColor }}; width: 100%; height: 22mm; }
#hdr .band-tbl {
  width: 100%;
  height: 22mm;
  border-collapse: collapse;
  padding: 0 16mm;
}
#hdr .col-logo { width: 18mm; vertical-align: middle; padding-right: 4mm; }
#hdr .logo-img {
  display: block;
  width: 13mm; height: 13mm;
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
#hdr .title { font-size: 14pt; font-weight: bold; color: #ffffff; line-height: 1; }
#hdr .org { font-size: 7.5pt; color: {{ $pale }}; margin-top: 2px; }
#hdr .col-meta { width: 55mm; vertical-align: middle; text-align: right; }
#hdr .mline { font-size: 6.5pt; color: {{ $pale }}; line-height: 1.8; }
#hdr .mline b { color: #ffffff; }
#hdr .accent { height: 2.5mm; background: {{ $darker }}; width: 100%; }
#hdr .corp {
  height: 5.5mm;
  background: {{ $xlight }};
  border-bottom: 1px solid {{ $pale }};
  width: 100%;
  padding: 0 16mm;
}
#hdr .corp-tbl { width: 100%; height: 5.5mm; border-collapse: collapse; }
#hdr .corp-td {
  font-size: 6pt;
  color: #475569;
  padding-right: 8mm;
  vertical-align: middle;
  white-space: nowrap;
}
#hdr .dot { color: {{ $primaryColor }}; margin-right: 1.5px; }

/* ── FOOTER FIJO ── */
#ftr {
  position: fixed;
  bottom: 0;
  left:   0;
  right:  0;
  width:  100%;
  height: 13mm;
  background: #ffffff;
}
#ftr .fline { height: 1.5px; background: {{ $primaryColor }}; width: 100%; }
#ftr .fbody { width: 100%; border-collapse: collapse; padding: 0 16mm; }
#ftr .fl { font-size: 6pt; color: #94a3b8; vertical-align: top; padding-top: 2mm; }
#ftr .fl b { color: {{ $primaryColor }}; font-size: 6.5pt; }
#ftr .fr { font-size: 6pt; color: #94a3b8; text-align: right; vertical-align: top; padding-top: 2mm; }
.pagenum:before   { content: counter(page); }
.pagetotal:before { content: counter(pages); }

/* ── CONTENIDO ── */
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
.stats td.hi { background: {{ $xlight }}; border-top-color: {{ $darker }}; }
.sn { font-size: 13pt; font-weight: bold; line-height: 1; }
.sl { font-size: 5pt; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.3px; }
.cp { color: {{ $primaryColor }}; }
.cg { color: #059669; }
.cb { color: #2563eb; }

/* Tabla principal de roles */
.dt {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
  margin-bottom: 8px;
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
  vertical-align: top;
  overflow: hidden;
}
.dt tbody td:last-child { border-right: none; }
.dt tbody tr:last-child td { border-bottom: 2px solid {{ $pale }}; }
.tc { text-align: center; }
.rn { font-weight: bold; color: #0f172a; font-size: {{ $isL ? '7pt' : '7.5pt' }}; }

.pill {
  display: inline-block;
  font-size: 5.5pt; font-weight: bold;
  color: {{ $primaryColor }};
  border: 1px solid {{ $primaryColor }};
  padding: 1px 3px; border-radius: 2px;
  margin: 1px 1px 0 0;
}
.pill-perm {
  display: inline-block;
  font-size: 5pt;
  color: #475569;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  padding: 0.5px 2.5px; border-radius: 2px;
  margin: 0.5px 0.5px 0 0;
}

.cnt {
  display: inline-block;
  width: 16px; height: 16px;
  line-height: 16px;
  text-align: center;
  border-radius: 50%;
  font-size: 7pt;
  font-weight: bold;
  background: {{ $primaryColor }};
  color: #fff;
}
.cnt-g { background: #059669; }
.cnt-b { background: #2563eb; }

.ts { color: #94a3b8; font-size: 5.5pt; }
</style>
</head>
<body>

{{-- ── HEADER FIJO ────────────────────────── --}}
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
          <div class="eyebrow">Reporte Oficial &nbsp;&bull;&nbsp; Gestión de Roles y Permisos</div>
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
          <div class="mline">Roles:&nbsp;<b>{{ $cTot }}</b></div>
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

{{-- ── FOOTER FIJO ────────────────────────── --}}
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

{{-- ── CONTENIDO ────────────────────────── --}}
@if($filters)
<div class="filter-box">Filtros: <b>{{ $filters }}</b></div>
@endif

<div class="sec">Resumen estadístico</div>
<table class="stats">
  <tr>
    <td class="hi"><div class="sn cp">{{ $cTot }}</div><div class="sl">Total Roles</div></td>
    <td><div class="sn cg">{{ $cUser }}</div><div class="sl">Usuarios asignados</div></td>
    <td><div class="sn cb">{{ $cPerm }}</div><div class="sl">Permisos totales</div></td>
    @foreach($roles as $r)
    <td>
      <div class="sn cp">{{ $r->users->count() }}</div>
      <div class="sl">{{ $r->name }}</div>
    </td>
    @endforeach
  </tr>
</table>

<div class="sec" style="margin-top:8px">
  Roles del sistema &mdash; {{ now()->format('d/m/Y H:i') }}
</div>

<table class="dt">
  <thead>
    <tr>
      <th class="tc" style="width:3%">#</th>
      <th style="width:{{ $isL?'15%':'20%' }}">Nombre del Rol</th>
      <th class="tc" style="width:{{ $isL?'8%':'10%' }}">Usuarios</th>
      <th class="tc" style="width:{{ $isL?'8%':'10%' }}">Permisos</th>
      <th style="width:{{ $isL?'56%':'47%' }}">Permisos asignados</th>
      @if($isL)<th class="tc" style="width:10%">Creado</th>@endif
    </tr>
  </thead>
  <tbody>
    @foreach($roles as $i => $role)
    <tr class="r{{ $i%2 }}">
      <td class="tc ts">{{ $i+1 }}</td>
      <td>
        <div class="rn">{{ $role->name }}</div>
        @if(!$isL)<div class="ts">{{ $role->created_at?->format('d/m/Y') ?? '---' }}</div>@endif
      </td>
      <td class="tc">
        <span class="cnt cnt-g">{{ $role->users->count() }}</span>
      </td>
      <td class="tc">
        <span class="cnt cnt-b">{{ $role->permissions->count() }}</span>
      </td>
      <td>
        @forelse($role->permissions->sortBy('name') as $perm)
          <span class="pill-perm">{{ $perm->name }}</span>
        @empty
          <span class="ts">Sin permisos</span>
        @endforelse
      </td>
      @if($isL)
        <td class="tc ts">{{ $role->created_at?->format('d/m/Y') ?? '---' }}</td>
      @endif
    </tr>

    @if($showUsers && $role->users->count() > 0)
    <tr class="r{{ $i%2 }}">
      <td></td>
      <td colspan="{{ $isL ? 5 : 4 }}" style="padding: 2px 4px 6px; border-bottom: 1px solid #e2e8f0;">
        <div style="font-size:5.5pt; color:#64748b; font-weight:bold; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.5px;">
          Usuarios con este rol:
        </div>
        @foreach($role->users->take(20) as $u)
          <span class="pill">{{ $u->name }}</span>
        @endforeach
        @if($role->users->count() > 20)
          <span class="ts"> +{{ $role->users->count()-20 }} mas</span>
        @endif
      </td>
    </tr>
    @endif
    @endforeach
  </tbody>
</table>

</body>
</html>
