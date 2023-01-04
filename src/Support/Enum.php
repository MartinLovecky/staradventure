<?php

namespace Mlkali\Sa\Support;

class Enum
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...
    const ARTICLE_CREATED = 'success.Stránka %s vytvořena';
    const ARTICLE_UPDATED = 'success.Příběh %s upraven';
    const ARTICLE_DELETED = 'success.Stránka %s smazána';
    const ARTICLE_DOES_ALLREADY_EXIST = 'warning.Stránka %s již existuje použite <a href="/update/%s/%s">update</a>';
    const ARTICLE_DOES_NOT_EXIST = 'warning.Stránka %s neexistuje použite <a href="/create/%s/%s">create</a>';
    const VALIDATION_CRSF_ERROR = 'danger.Csfr validation failed';
    const VALIDATION_PASSWORD_REGEX = 'danger.Heslo musí obasahovat nejméně jedno malé a velké písmeno a jeden specialní znak(!@$%^&)';
    const VALIDATION_EMAIL_FORMAT = 'danger.Nesprávný formát emailu (%s)';
    const VALIDATION_FORGOTEN_USER = 'danger.Email (%s) neexistuje v db';
    const VALIDATION_REG_CHECKBOX_FAIL = 'danger.checkbox failed';
    const VALIDATION_USER_NOT_EXIST = 'danger.Uživatel %s neexistuje';
    const VALIDATION_USER_ALREADY_EXISTS = 'danger.Member %s alredy exists';
    const VALIDATION_LEN_PASSWORD = 'danger.Heslo musí obsahovat nejméně 6 znaků';
    const VALIDATION_PASSWORD_AGAIN = 'danger.Hesla se musí schodovat';
    const VALIDATION_LEN_USER = 'danger.Username %s musí mít nejméně 4 znaky';
    const AVATAR_UPLOAD = 'danger.Avatar musí být nahrán';
    const AVATAR_SIZE = 'danger.Avatar nesmí mít více jak 5MB';
    const AVATAR_MIME_TYPE = 'danger.File musí být png nebo jpg';
    const REQUETS_REGISTER = 'success.Byl vám odeslán aktivační email na %s';
    const REQUETS_LOGIN = 'success.Vítejte zpět %s';
    const REQUETS_RESET_SEND = 'success.Odkaz na změnu hesla byl odeslán na %s';
    const REQUETS_RESET_PASSWORD = 'success.Heslo bylo úspěšně změněno';
    const REQUETS_FORGOTEN_USER = 'succes.Uživatelské jméno bylo zasláno na váš %s';
    const REQUEST_LOGOUT = 'success.Úspěšně odhlášen';
    const REQUEST_ACTIVATE = 'success.Aktivace úspěšná můžete se přihlásit';
    const REQUEST_ACTIVATE_FAIL = 'danger.Aktivace účtu se nezdařila kontaktujte podporu';
    const REQUEST_PERMISSION = 'success.Práva účtu změněna';
    const REQUEST_DELETE = 'success.Účet úspěšně smazán';

    const MAIN_EMAIL_TEMPLATE = '<div style="background:#f7f7f7;font-family:Arial,sans-serif;font-size:14px;padding:20px 0;color:#000"><table style="background:#fff;margin:0 auto;max-width:800px;padding:20px 40px;width:100%">
    <tbody><tr><td valign="top"><table cellspacing="0" cellpadding="0" style="width:100%"><tbody><tr><td class="m_-3655226564915594624header__logo" style="padding:10px 0;text-align:left">
    <a href="%s/#index" style="color:#ec028c" target="_blank"><img src="%s/public/img/android-chrome-256x256.png" width="200" height="44" border="0" class="CToWUd" alt="logo"></a></td></tr></tbody></table>TEMPLATE</td></tr></tbody></table></div>';

    const EMAIL_TEMPLATE_REGISTER = '<p style="font-size:15px;line-height:22px;margin:15px 0;padding:0;font-weight:bold;color:#000">Dobrý den,</p>
    <p style="font-size:15px;line-height:22px;margin:15px 0;padding:0;color:#000">Přihlášení na StarAdventure provedete pomocí uživatelského jména <strong>%s</strong>. Z bezpečnostních důvodů neposíláme Vaše heslo.</p>
    <span style="text-align:center;display:block">
    <a href="%s/activate?x=%s&amp;id=%s&amp;token=%s" style="background:#28a745;border-radius:4px;color:#fff;display:inline-block;font-weight:700;margin:16px auto 32px;padding:15px 25px;text-decoration:none" target="_blank">Aktivovat účet &nbsp;»</a></span>
    <p style="font-size:15px;line-height:22px;margin:15px 0;padding:0">Hezký den Vám přeje Admin StarAdventure &#169; Sensei</p>';

    const EMAIL_TEMPLATE_RESET = '<p style="font-size:15px;line-height:22px;margin:15px 0;padding:0;font-weight:bold;color:#000">Resetovat heslo?</p>
    <p style="font-size:15px;line-height:22px;margin:15px 0;padding:0;color:#000">Pokud jste nám poslali požadavek na obnovení hesla účtu <strong>%s</strong>. dokončete proces kliknutím na "Reset Hesla". Pokud jste to nebyli vy, kdo požadavek poslal, můžete tento e-mail ignorovat.</p>
    <span style="text-align:center;display:block">
    <a href="%s/newpassword?x=%s&amp;id=%s&amp;token=%s#newpassword" style="background:#28a745;border-radius:4px;color:#fff;display:inline-block;font-weight:700;margin:16px auto 32px;padding:15px 25px;text-decoration:none" target="_blank">Reset Hesla &nbsp;»</a></span>
    <p style="font-size:15px;line-height:22px;margin:15px 0;padding:0">Hezký den Vám přeje Admin StarAdventure &#169; Sensei</p>';

    const EMAIL_TEMPLATE_USER = '<p style="font-size:15px;line-height:22px;margin:15px 0;padding:0;font-weight:bold;color:#000">Uživatelské jméno</p>
    <p style="font-size:15px;line-height:22px;margin:15px 0;padding:0;color:#000">Pokud jste nám poslali požadavek na jméno <strong>%s</strong>. dokončete proces kliknutím na "Získat jméno". Pokud jste to nebyli vy, kdo požadavek poslal, můžete tento e-mail ignorovat.</p>
    <span style="text-align:center;display:block">
    <a href="%s/newpassword?x=%s&amp;id=%s&amp;token=%s#newpassword" style="background:#28a745;border-radius:4px;color:#fff;display:inline-block;font-weight:700;margin:16px auto 32px;padding:15px 25px;text-decoration:none" target="_blank">Získat jméno &nbsp;»</a></span>
    <p style="font-size:15px;line-height:22px;margin:15px 0;padding:0">Hezký den Vám přeje Admin StarAdventure &#169; Sensei</p>';
}
