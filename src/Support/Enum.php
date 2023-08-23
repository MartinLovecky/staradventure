<?php

namespace Mlkali\Sa\Support;

class Enum
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...
    public const ARTICLE_CREATED = 'success_Stránka %s vytvořena';
    public const ARTICLE_UPDATED = 'success_Příběh %s upraven';
    public const ARTICLE_DELETED = 'success_Stránka %s smazána';
    public const ARTICLE_DOES_ALLREADY_EXIST = 'warning_Stránka %s již existuje použite <a href="/update/%s/%s">update</a>';
    public const ARTICLE_DOES_NOT_EXIST = 'warning_Stránka %s neexistuje použite <a href="/create/%s/%s">create</a>';
    public const VALIDATION_CRSF_ERROR = 'danger_Csfr validation failed';
    public const VALIDATION_PASSWORD_REGEX = 'danger_Heslo musí obasahovat nejméně jedno malé a velké písmeno a jeden specialní znak(!@$%^&)';
    public const VALIDATION_EMAIL_FORMAT = 'danger_Nesprávný formát emailu (%s)';
    public const VALIDATION_FORGOTEN_USER = 'danger_Email (%s) neexistuje v db';
    public const VALIDATION_REG_CHECKBOX_FAIL = 'danger_checkbox failed';
    public const VALIDATION_USER_NOT_EXIST = 'danger_Uživatel %s neexistuje';
    public const VALIDATION_USER_ALREADY_EXISTS = 'danger_Member %s alredy exists';
    public const VALIDATION_LEN_PASSWORD = 'danger_Heslo musí obsahovat nejméně 6 znaků';
    public const VALIDATION_PASSWORD_AGAIN = 'danger_Hesla se musí schodovat';
    public const VALIDATION_LEN_USER = 'danger_Username %s musí mít nejméně 4 znaky';
    public const VALIDATION_ACTIVE_MEMBER = 'danger_Účet není aktivován';
    public const INVALID_URL = 'danger_Nesprávné url zkontrolujte si email';
    public const USER_NOT_LOGGED = 'danger_Pro zobrazení stránky se musíte přihlásit';
    public const USER_LOGGED = 'warning_Stránku reset nelze otevřít když jste přihlášen';
    public const USER_PERMISSION = 'danger_Nemáte přístup k zobrazení stránky';
    public const AVATAR_UPLOAD = 'danger_Avatar musí být nahrán';
    public const AVATAR_SIZE = 'danger_Avatar nesmí mít více jak 5MB';
    public const AVATAR_MIME_TYPE = 'danger_File musí být png nebo jpg';
    public const REQUETS_REGISTER = 'success_Byl vám odeslán aktivační na email %s';
    public const REQUETS_LOGIN = 'success_Vítejte zpět %s';
    public const REQUETS_RESET_SEND = 'success_Odkaz na změnu hesla byl odeslán na %s';
    public const REQUETS_RESET_PASSWORD = 'success_Heslo bylo úspěšně změněno';
    public const REQUETS_FORGOTEN_USER = 'succes_Uživatelské jméno bylo zasláno na váš %s';
    public const REQUEST_LOGOUT = 'success_Úspěšně odhlášen';
    public const REQUEST_ACTIVATE = 'success_Aktivace úspěšná můžete se přihlásit';
    public const REQUEST_ACTIVATE_FAIL = 'danger_Aktivace účtu se nezdařila kontaktujte podporu';
    public const REQUEST_PERMISSION = 'success_Práva účtu změněna';
    public const REQUEST_DELETE = 'success_Účet úspěšně smazán';
}
