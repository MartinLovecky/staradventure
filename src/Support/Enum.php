<?php

namespace Mlkali\Sa\Support;

class Enum
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...
    const ARTICLE_CREATED = 'success_Stránka %s vytvořena';
    const ARTICLE_UPDATED = 'success_Příběh %s upraven';
    const ARTICLE_DELETED = 'success_Stránka %s smazána';
    const ARTICLE_DOES_ALLREADY_EXIST = 'warning_Stránka %s již existuje použite <a href="/update/%s/%s">update</a>';
    const ARTICLE_DOES_NOT_EXIST = 'warning_Stránka %s neexistuje použite <a href="/create/%s/%s">create</a>';
    const VALIDATION_CRSF_ERROR = 'danger_Csfr validation failed';
    const VALIDATION_PASSWORD_REGEX = 'danger_Heslo musí obasahovat nejméně jedno malé a velké písmeno a jeden specialní znak(!@$%^&)';
    const VALIDATION_EMAIL_FORMAT = 'danger_Nesprávný formát emailu (%s)';
    const VALIDATION_FORGOTEN_USER = 'danger_Email (%s) neexistuje v db';
    const VALIDATION_REG_CHECKBOX_FAIL = 'danger_checkbox failed';
    const VALIDATION_USER_NOT_EXIST = 'danger_Uživatel %s neexistuje';
    const VALIDATION_USER_ALREADY_EXISTS = 'danger_Member %s alredy exists';
    const VALIDATION_LEN_PASSWORD = 'danger_Heslo musí obsahovat nejméně 6 znaků';
    const VALIDATION_PASSWORD_AGAIN = 'danger_Hesla se musí schodovat';
    const VALIDATION_LEN_USER = 'danger_Username %s musí mít nejméně 4 znaky';
    const VALIDATION_ACTIVE_MEMBER = 'danger_Účet není aktivován';
    const AVATAR_UPLOAD = 'danger_Avatar musí být nahrán';
    const AVATAR_SIZE = 'danger_Avatar nesmí mít více jak 5MB';
    const AVATAR_MIME_TYPE = 'danger_File musí být png nebo jpg';
    const REQUETS_REGISTER = 'success_Byl vám odeslán aktivační email na %s';
    const REQUETS_LOGIN = 'success_Vítejte zpět %s';
    const REQUETS_RESET_SEND = 'success_Odkaz na změnu hesla byl odeslán na %s';
    const REQUETS_RESET_PASSWORD = 'success_Heslo bylo úspěšně změněno';
    const REQUETS_FORGOTEN_USER = 'succes_Uživatelské jméno bylo zasláno na váš %s';
    const REQUEST_LOGOUT = 'success_Úspěšně odhlášen';
    const REQUEST_ACTIVATE = 'success_Aktivace úspěšná můžete se přihlásit';
    const REQUEST_ACTIVATE_FAIL = 'danger_Aktivace účtu se nezdařila kontaktujte podporu';
    const REQUEST_PERMISSION = 'success_Práva účtu změněna';
    const REQUEST_DELETE = 'success_Účet úspěšně smazán';

}