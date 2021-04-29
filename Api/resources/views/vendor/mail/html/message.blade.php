@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<span style="font-style: italic;">Perhatian: e-Mail ini (termasuk seluruh lampirannya, bila ada) hanya ditujukan kepada penerima yang tercantum di atas. Jika Anda bukan penerima yang dituju, maka Anda tidak diperkenankan untuk menyimpan, menyebarkan, menggandakan, mendistribusikan, atau memanfaatkan e-Mail ini beserta seluruh lampirannya. Jika Anda secara tidak sengaja menerima e-Mail ini, mohon kerjasamanya untuk segera memberitahukan ke alamat e-Mail pengirim serta menghapus e-Mail ini beserta seluruh lampirannya. Anda juga harus memeriksa e-Mail ini beserta lampirannya untuk keberadaan virus. Kami tidak bertanggung jawab atas kerugian yang ditimbulkan oleh virus yang ditularkan melalui e-Mail ini.</span>
@endcomponent
@endslot
@endcomponent
