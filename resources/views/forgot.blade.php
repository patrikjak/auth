@section('title', __('pjauth::pages.titles.reset_password'))

<x-pjauth::layouts.app :title="__('pjauth::pages.titles.reset_password')">

    <x-slot:links>
        <p><a href="{{ route('login') }}" class="primary-color">@lang('pjauth::pages.password.login')</a></p>
    </x-slot:links>

    <x-slot:image>
        <img src="{{ asset('vendor/pjauth/assets/images/illustrations/forgot_password.svg') }}" alt="Forgot password">
    </x-slot:image>

    <p>@lang('pjauth::pages.password.forgot.intro')</p>

    <x-pjutils::form
        :action="route('api.password.email')"
        :action-label="__('pjauth::pages.password.forgot.action')"
        data-recaptcha-action="forgot"
    >
        <x-pjutils::form.email name="email"
                               :label="__('pjauth::forms.email')"
                               :placeholder="__('pjauth::forms.placeholders.email')"
                               autocomplete="email"
                               :autofocus="true"
        />
    </x-pjutils::form>

</x-pjauth::layouts.app>
