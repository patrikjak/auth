@section('title', __('pjauth::pages.titles.register'))

<x-pjauth::layouts.app :title="__('pjauth::pages.titles.register')">

    <x-slot:links>
        <p>@lang('pjauth::pages.register.existing_account') <a href="{{ '#' }}" class="primary-color">@lang('pjauth::pages.register.login')</a></p>
    </x-slot:links>

    <x-slot:image>
        <img src="{{ asset('vendor/pjauth/assets/images/illustrations/welcome.svg') }}" alt="welcome">
    </x-slot:image>

    <p>@lang('pjauth::pages.register.intro')</p>

    <x-pjutils::form :action="'#'" :action-label="__('pjauth::pages.register.action')">
        <x-pjutils::form.input name="name"
                               :label="__('pjauth::forms.name')"
                               :placeholder="__('pjauth::forms.placeholders.name')"
                               autocomplete="name"
                               :autofocus="true"
        />

        <x-pjutils::form.email name="email"
                               :label="__('pjauth::forms.email')"
                               :placeholder="__('pjauth::forms.placeholders.email')"
                               autocomplete="email"
        />

        <x-pjutils::form.password name="password"
                                  :label="__('pjauth::forms.password')"
                                  :placeholder="__('pjauth::forms.placeholders.password')"
                                  autocomplete="new-password"
        />
    </x-pjutils::form>

    @if(config('pjauth.social_login.google.enabled'))
        <p class="center" id="or">@lang('pjauth::pages.register.or_use_social')</p>

        <div class="socials">
            <x-pjauth::google-login-button :label="__('pjauth::pages.register.google')" />
        </div>
    @endif

</x-pjauth::layouts.app>
