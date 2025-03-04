import {bindPasswordVisibilitySwitch} from "../../vendor/patrikjak/utils/resources/assets/js/form/helper";
import Form from "../../vendor/patrikjak/utils/resources/assets/js/form/Form";
import {getData} from "../../vendor/patrikjak/utils/resources/assets/js/helpers/general";

bindPasswordVisibilitySwitch();

window['RECAPTCHA_SITE_KEY'] = getData(document.body, 'recaptcha-site-key');

const form: HTMLElement = document.querySelector('.pj-auth form');
const recaptchaAction = getData(form, 'recaptcha-action');

new Form()
    .setRecaptchaAction(recaptchaAction)
    .bindSubmit();