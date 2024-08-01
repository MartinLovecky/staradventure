<?php

namespace Mlkali\Sa\Support;

class Enum
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...

    // Success Messages
    public const ARTICLE_CREATED = 'success_Page %s created';
    public const ARTICLE_UPDATED = 'success_Page %s updated';
    public const ARTICLE_DELETED = 'success_Page %s deleted';
    public const REQUEST_REGISTER = 'success_Activation link has been sent to email %s';
    public const REQUEST_LOGIN = 'success_Welcome back %s';
    public const REQUEST_RESET_SEND = 'success_Password reset link has been sent to %s';
    public const REQUEST_RESET_PASSWORD = 'success_Password has been successfully changed';
    public const REQUEST_FORGOTTEN_USER = 'success_Username has been sent to your %s';
    public const REQUEST_LOGOUT = 'success_You have been successfully logged out';
    public const REQUEST_ACTIVATE = 'success_Activation successful, you can now log in';
    public const REQUEST_PERMISSION = 'success_Account permissions updated';
    public const REQUEST_DELETE = 'success_Account successfully deleted';

    // Warning Messages
    public const ARTICLE_ALREADY_EXISTS = 'warning_Page %s already exists. Use <a href="/update/%s/%s">update</a>';
    public const ARTICLE_DOES_NOT_EXIST = 'warning_Page %s does not exist. Use <a href="/create/%s/%s">create</a>';
    public const USER_LOGGED = 'warning_You cannot access the reset page while logged in';

    // Danger Messages
    public const VALIDATION_CSRF_ERROR = 'danger_CSRF validation failed';
    public const VALIDATION_PASSWORD_REGEX = 'danger_Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (!@$%^&)';
    public const VALIDATION_EMAIL_FORMAT = 'danger_Invalid email format (%s)';
    public const VALIDATION_FORGOTTEN_USER = 'danger_Email (%s) does not exist in the database';
    public const VALIDATION_REG_CHECKBOX_FAIL = 'danger_Checkbox validation failed';
    public const VALIDATION_USER_NOT_EXIST = 'danger_User %s does not exist';
    public const VALIDATION_USER_ALREADY_EXISTS = 'danger_User %s already exists';
    public const VALIDATION_LEN_PASSWORD = 'danger_Password must be at least 6 characters long';
    public const VALIDATION_PASSWORD_AGAIN = 'danger_Passwords must match';
    public const VALIDATION_LEN_USER = 'danger_Username %s must be at least 4 characters long';
    public const VALIDATION_ACTIVE_MEMBER = 'danger_Account is not activated';
    public const INVALID_URL = 'danger_Invalid URL, please check your email';
    public const USER_NOT_LOGGED = 'danger_You must be logged in to view this page';
    public const USER_PERMISSION = 'danger_You do not have permission to view this page';
    public const AVATAR_UPLOAD = 'danger_Avatar must be uploaded';
    public const AVATAR_SIZE = 'danger_Avatar must not exceed 5MB';
    public const AVATAR_MIME_TYPE = 'danger_File must be PNG or JPG';
    public const REQUEST_ACTIVATE_FAIL = 'danger_Account activation failed, please contact support';
}
