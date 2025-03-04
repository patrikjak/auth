@section('title', __('pjauth::pages.titles.reset_password'))

<x-pjauth::layouts.app :title="__('pjauth::pages.titles.reset_password')">

    <x-slot:links>
        <p><a href="{{ route('login') }}" class="primary-color">@lang('pjauth::pages.password.login')</a></p>
    </x-slot:links>

    <x-slot:image>
        <img src="{{ asset('vendor/pjauth/assets/images/illustrations/safe.svg') }}" alt="Safe">
    </x-slot:image>

    <p>@lang('pjauth::pages.password.reset.intro')</p>

    <x-pjutils::form
        :action="route('api.password.store')"
        :action-label="__('pjauth::pages.password.reset.action')"
        data-recaptcha-action="reset"
        method="PATCH"
    >
        <x-pjutils::form.input type="hidden" name="token" :value="$token" />

        <x-pjutils::form.email name="email"
                               :label="__('pjauth::forms.email')"
                               :placeholder="__('pjauth::forms.placeholders.email')"
                               autocomplete="email"
                               :autofocus="true"
                               :value="$email"
        />

        <x-pjutils::form.password name="password"
                                  :label="__('pjauth::forms.password')"
                                  :placeholder="__('pjauth::forms.placeholders.password')"
                                  autocomplete="new-password"
                                  :confirm="true"
                                  :confirm-label="__('pjauth::forms.password_confirmation')"
                                  :confirm-placeholder="__('pjauth::forms.placeholders.password_confirmation')"
        />
    </x-pjutils::form>

</x-pjauth::layouts.app>
