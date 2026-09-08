@php $favicons = \App\Http\Controllers\Admin\BrandingController::faviconLinks(); @endphp
<link rel="icon" href="{{ $favicons['ico'] }}" sizes="32x32">
@if ($favicons['svg'])
    <link rel="icon" type="image/svg+xml" href="{{ $favicons['svg'] }}">
@endif
<link rel="icon" type="image/png" sizes="32x32" href="{{ $favicons['png32'] }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ $favicons['png16'] }}">
<link rel="apple-touch-icon" href="{{ $favicons['apple'] }}">
