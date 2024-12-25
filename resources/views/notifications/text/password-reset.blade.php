@lang('pjauth::notifications.reset_password.title')
@lang('pjauth::notifications.reset_password.text.intro')
@lang('pjauth::notifications.reset_password.text.action')
@lang('pjauth::notifications.reset_password.text.expire', ['expire' => $expireIn])
@lang('pjauth::notifications.reset_password.text.outro')
@lang('pjauth::notifications.reset_password.text.trouble', ['action' => __('pjauth::notifications.reset_password.text.action')])
{{ $resetUrl }}