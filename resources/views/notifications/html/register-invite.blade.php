<x-pjutils::email.layout :use-logo="false">
    <h1>@lang('pjauth::notifications.register_invite.title')</h1>
    <p>@lang('pjauth::notifications.register_invite.text.intro')</p>
    <div class="background center">
        <a href="{{ $registerUrl }}" class="button-like">@lang('pjauth::notifications.register_invite.text.action')</a>
    </div>
</x-pjutils::email.layout>