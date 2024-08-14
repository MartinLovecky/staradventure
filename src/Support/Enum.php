<?php

namespace Mlkali\Sa\Support;

class Enum
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...

    // Success Messages
    const ARTICLE_CREATED = 'success_Page %s created';
    const ARTICLE_UPDATED = 'success_Page %s updated';
    const ARTICLE_DELETED = 'success_Page %s deleted';
    const REQUEST_REGISTER = 'success_Activation link has been sent to email %s';
    const REQUEST_LOGIN = 'success_Welcome back %s';
    const REQUEST_RESET_SEND = 'success_Password reset link has been sent to %s';
    const REQUEST_RESET_PASSWORD = 'success_Password has been successfully changed';
    const REQUEST_FORGOTTEN_USER = 'success_Username has been sent to your %s';
    const REQUEST_LOGOUT = 'success_You have been successfully logged out';
    const REQUEST_ACTIVATE = 'success_Activation successful, you can now log in';
    const REQUEST_PERMISSION = 'success_Account permissions updated';
    const REQUEST_DELETE = 'success_Account successfully deleted';

    // Warning Messages
    const ARTICLE_ALREADY_EXISTS = 'warning_Page %s already exists. Use <a href="/update/%s/%s">update</a>';
    const ARTICLE_DOES_NOT_EXIST = 'warning_Page %s does not exist. Use <a href="/create/%s/%s">create</a>';
    const USER_LOGGED = 'warning_You cannot access the reset page while logged in';

    // Danger Messages
    const VALIDATION_CSRF_ERROR = 'danger_CSRF validation failed';
    const VALIDATION_PASSWORD_REGEX = 'danger_Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (!@$%^&)';
    const VALIDATION_EMAIL_FORMAT = 'danger_Invalid email format (%s)';
    const VALIDATION_FORGOTTEN_USER = 'danger_Email (%s) does not exist in the database';
    const VALIDATION_REG_CHECKBOX_FAIL = 'danger_Checkbox validation failed';
    const VALIDATION_USER_NOT_EXIST = 'danger_User %s does not exist';
    const VALIDATION_USER_ALREADY_EXISTS = 'danger_User %s already exists';
    const VALIDATION_LEN_PASSWORD = 'danger_Password must be at least 6 characters long';
    const VALIDATION_PASSWORD_AGAIN = 'danger_Passwords must match';
    const VALIDATION_LEN_USER = 'danger_Username %s must be at least 4 characters long';
    const VALIDATION_ACTIVE_MEMBER = 'danger_Account is not activated';
    const INVALID_URL = 'danger_Invalid URL, please check your email';
    const USER_NOT_LOGGED = 'danger_You must be logged in to view this page';
    const USER_PERMISSION = 'danger_You do not have permission to view this page';
    const AVATAR_UPLOAD = 'danger_Avatar must be uploaded';
    const AVATAR_SIZE = 'danger_Avatar must not exceed 5MB';
    const AVATAR_MIME_TYPE = 'danger_File must be PNG or JPG';
    const REQUEST_ACTIVATE_FAIL = 'danger_Account activation failed, please contact support';
}
