<x-pjutils::email.layout :use-logo="false">
    <h1>@lang('pjauth::notifications.reset_password.title')</h1>
    <p>@lang('pjauth::notifications.reset_password.text.intro')</p>
    <div class="background center">
        <a href="{{ $resetUrl }}" class="button-like">@lang('pjauth::notifications.reset_password.text.action')</a>
    </div>
    <p class="space-top">@lang('pjauth::notifications.reset_password.text.expire', ['expire' => $expireIn])</p>
    <p class="space-top">@lang('pjauth::notifications.reset_password.text.outro')</p>
    <p class="space-top">@lang('pjauth::notifications.reset_password.text.trouble', ['action' => __('pjauth::notifications.reset_password.text.action')])</p>
    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
</x-pjutils::email.layout>