@php
$containerFooter =
isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
? 'container-xxl'
: 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
    <div class="{{ $containerFooter }}">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body">
                &#169; {{ date('Y') }}, {{ setting('company_name', setting('site_name', 'Mi Sistema')) }}. Todos los derechos reservados.
            </div>
            @if(setting('company_website'))
            <div class="d-none d-lg-inline-block">
                <a href="{{ setting('company_website') }}" class="footer-link" target="_blank">{{ setting('company_name', setting('site_name', '')) }}</a>
            </div>
            @endif
        </div>
    </div>
</footer>
<!-- / Footer -->
