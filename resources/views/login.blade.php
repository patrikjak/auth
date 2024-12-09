@section('title', __('pjauth::pages.titles.login'))

<x-pjauth::layouts.app :title="__('pjauth::pages.titles.login')">

    <x-slot:links>
        <p>@lang('pjauth::pages.login.no_account') <a href="{{ route('register') }}" class="primary-color">@lang('pjauth::pages.login.register')</a></p>
    </x-slot:links>

    <x-slot:image>
        <img src="{{ asset('vendor/pjauth/assets/images/illustrations/hello.svg') }}" alt="welcome">
    </x-slot:image>

    <p>@lang('pjauth::pages.login.intro') <a class="forgotten-password" href="{{ route('password.request') }}">Zabudli ste heslo?</a></p>

    <x-pjutils::form :action="'#'" :action-label="__('pjauth::pages.login.action')">
        <x-pjutils::form.email name="email"
                               :label="__('pjauth::forms.email')"
                               :placeholder="__('pjauth::forms.placeholders.email')"
                               autocomplete="email"
                               :autofocus="true"
        />

        <x-pjutils::form.password name="password"
                                  :label="__('pjauth::forms.password')"
                                  :placeholder="__('pjauth::forms.placeholders.password')"
                                  autocomplete="new-password"
        />
    </x-pjutils::form>

    @if(config('pjauth.social_login.google.enabled'))
        <p class="center" id="or">@lang('pjauth::pages.login.or_use_social')</p>

        <div class="socials">
            <x-pjauth::google-login-button />
        </div>
    @endif

</x-pjauth::layouts.app>
