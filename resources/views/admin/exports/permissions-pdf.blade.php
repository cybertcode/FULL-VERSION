@php
$hex    = ltrim($primaryColor, '#');
$rr     = hexdec(substr($hex, 0, 2));
$gg     = hexdec(substr($hex, 2, 2));
$bb     = hexdec(substr($hex, 4, 2));
$darker = sprintf('#%02x%02x%02x', max(0,$rr-60),   max(0,$gg-60),   max(0,$bb-60));
$pale   = sprintf('#%02x%02x%02x', min(255,$rr+195), min(255,$gg+195), min(255,$bb+195));
$xlight = sprintf('#%02x%02x%02x', min(255,$rr+230), min(255,$gg+230), min(255,$bb+230));

$cTot      = $permissions->count();
$cModules  = $permissions->groupBy(fn($p) => explode('.', $p->name)[0])->count();
$cAssigned = $permissions->filter(fn($p) => $p->roles->count() > 0)->count();
$grouped   = $permissions->groupBy(fn($p) => explode('.', $p->name)[0])->sortKeys();
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
@page {
  margin: 0;
  size: 210mm 297mm;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: DejaVu Sans, sans-serif;
  font-size: 8pt;
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
  top: 0; left: 0; right: 0;
  width: 100%; height: 30mm;
}
#hdr .band { background: {{ $primaryColor }}; width: 100%; height: 22mm; }
#hdr .band-tbl { width: 100%; height: 22mm; border-collapse: collapse; padding: 0 16mm; }
#hdr .col-logo { width: 18mm; vertical-align: middle; padding-right: 4mm; }
#hdr .logo-img {
  display: block; width: 13mm; height: 13mm;
  object-fit: contain; background: #fff; border-radius: 3px; padding: 1.5px;
}
#hdr .logo-placeholder {
  width: 13mm; height: 13mm;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 3px; display: block;
}
#hdr .col-info { vertical-align: middle; }
#hdr .eyebrow { font-size: 5.5pt; color: {{ $pale }}; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; margin-bottom: 2px; }
#hdr .title { font-size: 14pt; font-weight: bold; color: #ffffff; line-height: 1; }
#hdr .org { font-size: 7.5pt; color: {{ $pale }}; margin-top: 2px; }
#hdr .col-meta { width: 55mm; vertical-align: middle; text-align: right; }
#hdr .mline { font-size: 6.5pt; color: {{ $pale }}; line-height: 1.8; }
#hdr .mline b { color: #ffffff; }
#hdr .accent { height: 2.5mm; background: {{ $darker }}; width: 100%; }
#hdr .corp { height: 5.5mm; background: {{ $xlight }}; border-bottom: 1px solid {{ $pale }}; width: 100%; padding: 0 16mm; }
#hdr .corp-tbl { width: 100%; height: 5.5mm; border-collapse: collapse; }
#hdr .corp-td { font-size: 6pt; color: #475569; padding-right: 8mm; vertical-align: middle; white-space: nowrap; }
#hdr .dot { color: {{ $primaryColor }}; margin-right: 1.5px; }

/* ── FOOTER FIJO ── */
#ftr {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  width: 100%; height: 13mm; background: #ffffff;
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
  font-size: 6pt; font-weight: bold; text-transform: uppercase;
  letter-spacing: 1.5px; color: {{ $primaryColor }};
  border-bottom: 1.5px solid {{ $primaryColor }};
  padding-bottom: 2px; margin-bottom: 7px;
}
.filter-box {
  background: {{ $xlight }}; border-left: 3px solid {{ $primaryColor }};
  padding: 4px 8px; margin-bottom: 8px; font-size: 7pt; color: #475569;
}
.filter-box b { color: #0f172a; }

/* Stats */
.stats { width: 100%; border-collapse: separate; border-spacing: 4px 0; margin-bottom: 10px; table-layout: fixed; }
.stats td { border: 1px solid #e2e8f0; border-top: 3px solid {{ $primaryColor }}; padding: 7px 3px 6px; text-align: center; background: #fff; }
.stats td.hi { background: {{ $xlight }}; border-top-color: {{ $darker }}; }
.sn { font-size: 13pt; font-weight: bold; line-height: 1; }
.sl { font-size: 5pt; color: #64748b; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.3px; }
.cp { color: {{ $primaryColor }}; }
.cg { color: #059669; }
.cb { color: #2563eb; }

/* Tabla de permisos */
.dt { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 4px; }
.dt thead th {
  background: {{ $primaryColor }}; color: #fff;
  font-size: 6.5pt; font-weight: bold;
  padding: 4.5px 4px; text-align: left;
  border-right: 1px solid {{ $darker }}; overflow: hidden;
}
.dt thead th:last-child { border-right: none; }
.dt thead th.tc { text-align: center; }
.dt tbody tr.r0 td { background: #ffffff; }
.dt tbody tr.r1 td { background: {{ $xlight }}; }
.dt tbody td {
  font-size: 6.5pt; padding: 4px;
  border-bottom: 1px solid #e2e8f0;
  border-right: 1px solid #f1f5f9;
  vertical-align: middle; overflow: hidden;
}
.dt tbody td:last-child { border-right: none; }
.dt tbody tr:last-child td { border-bottom: 2px solid {{ $pale }}; }
.tc { text-align: center; }

.mod-header {
  background: {{ $pale }};
  font-size: 6pt; font-weight: bold; text-transform: uppercase;
  letter-spacing: 1px; color: {{ $darker }};
  padding: 3px 4px;
}
.pname { font-weight: bold; color: #0f172a; font-size: 7pt; }
.plabel { font-size: 6.5pt; color: #475569; }
.pill-role {
  display: inline-block; font-size: 5pt; font-weight: bold;
  color: {{ $primaryColor }}; border: 1px solid {{ $primaryColor }};
  padding: 0.5px 2.5px; border-radius: 2px; margin: 0.5px;
}
.ts { color: #94a3b8; font-size: 5.5pt; }
.action-badge {
  display: inline-block; font-size: 5pt; font-weight: bold;
  background: {{ $xlight }}; color: {{ $darker }};
  padding: 1px 3px; border-radius: 2px; border: 1px solid {{ $pale }};
}
</style>
</head>
<body>

{{-- HEADER --}}
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
          <div class="eyebrow">Reporte Oficial &nbsp;&bull;&nbsp; Catálogo de Permisos del Sistema</div>
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
          <div class="mline">Permisos:&nbsp;<b>{{ $cTot }}</b></div>
          <div class="mline">Papel:&nbsp;<b>A4 Vertical</b></div>
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

{{-- FOOTER --}}
<div id="ftr">
  <div class="fline"></div>
  <table class="fbody">
    <tr>
      <td class="fl">
        <b>{{ $siteName }}</b> &mdash; {{ $companyName }}
        @if($companyRuc) &mdash; RUC {{ $companyRuc }} @endif
        <br>Documento institucional confidencial &mdash; Catálogo de permisos del sistema.
      </td>
      <td class="fr">
        {{ now()->format('d/m/Y H:i') }}
        &bull; Pág.&nbsp;<span class="pagenum"></span>&nbsp;/&nbsp;<span class="pagetotal"></span>
      </td>
    </tr>
  </table>
</div>

{{-- CONTENIDO --}}
@if($filters)
<div class="filter-box">Filtros: <b>{{ $filters }}</b></div>
@endif

<div class="sec">Resumen estadístico</div>
<table class="stats">
  <tr>
    <td class="hi"><div class="sn cp">{{ $cTot }}</div><div class="sl">Total permisos</div></td>
    <td><div class="sn cb">{{ $cModules }}</div><div class="sl">Módulos</div></td>
    <td><div class="sn cg">{{ $cAssigned }}</div><div class="sl">Asignados a roles</div></td>
    <td><div class="sn" style="color:#dc2626;">{{ $cTot - $cAssigned }}</div><div class="sl">Sin asignar</div></td>
  </tr>
</table>

<div class="sec" style="margin-top:8px">
  Permisos agrupados por módulo &mdash; {{ now()->format('d/m/Y H:i') }}
</div>

<table class="dt">
  <thead>
    <tr>
      <th class="tc" style="width:4%">#</th>
      <th style="width:32%">Permiso (label)</th>
      <th style="width:22%">Nombre técnico</th>
      <th class="tc" style="width:10%">Acción</th>
      <th style="width:32%">Roles asignados</th>
    </tr>
  </thead>
  <tbody>
    @php $rowIdx = 0; @endphp
    @foreach($grouped as $module => $perms)
    <tr>
      <td colspan="5" class="mod-header">{{ strtoupper($module) }} &nbsp;({{ $perms->count() }} permisos)</td>
    </tr>
    @foreach($perms as $p)
    <tr class="r{{ $rowIdx % 2 }}">
      <td class="tc ts">{{ ++$rowIdx }}</td>
      <td>
        <div class="pname">{{ $p->label ?? $p->name }}</div>
      </td>
      <td><span class="ts">{{ $p->name }}</span></td>
      <td class="tc">
        <span class="action-badge">{{ explode('.', $p->name)[1] ?? '—' }}</span>
      </td>
      <td>
        @forelse($p->roles->sortBy('name') as $role)
          <span class="pill-role">{{ $role->name }}</span>
        @empty
          <span class="ts">Sin asignar</span>
        @endforelse
      </td>
    </tr>
    @endforeach
    @endforeach
  </tbody>
</table>

</body>
</html>
