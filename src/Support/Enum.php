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
    const VALIDATION_REG_CHECKBOX_FAIL = 'danger.checkbox failed';
    const VALIDATION_USER_NOT_EXIST = 'danger.Uživatel %s neexistuje';
    const VALIDATION_USER_ALREADY_EXISTS = 'danger.Member %s alredy exists';
    const VALIDATION_LEN_PASSWORD = 'danger.Heslo musí obsahovat nejméně 6 znaků';
    const VALIDATION_PASSWORD_AGAIN = 'danger.Hesla se musí schodovat';
    const VALIDATION_LEN_USER = 'danger.Username %s musí mít nejméně 4 znaky';
    const AVATAR_UPLOAD = 'danger.Avatar musí být nahrán';
    const AVATAR_SIZE = 'danger.Avatar nesmí mít více jak 5MB';
    const AVATAR_MIME_TYPE = 'danger.File musí být png nebo jpg';
}