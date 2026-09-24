<x-mail::message>
# {{ __('Hi :name,', ['name' => $name]) }}

{{ __('Thanks for requesting an account with :app. Confirm this is really your email address to send your request through for review.', ['app' => config('app.name')]) }}

<x-mail::button :url="$url">
{{ __('Confirm Email Address') }}
</x-mail::button>

{{ __("If you didn't request this, you can safely ignore this email — nothing happens until you confirm.") }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>
