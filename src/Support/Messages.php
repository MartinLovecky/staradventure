<?php

declare(strict_types=1);

namespace Mlkali\Sa\Support;

class Messages
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...

    // Success Messages
    public const string ARTICLE_CREATED = 'success_Page %s created %s';
    public const string ARTICLE_UPDATED = 'success_Page %s updated %s';
    public const string ARTICLE_DELETED = 'success_Page %s deleted %s';
    public const string REQUEST_REGISTER = 'success_Activation link has been sent to email %s';
    public const string REQUEST_LOGIN = 'success_Welcome back %s';
    public const string REQUEST_RESET_SEND = 'success_Password reset link has been sent to %s';
    public const string REQUEST_RESET_PASSWORD = 'success_Password has been successfully changed';
    public const string REQUEST_FORGOTTEN_USER = 'success_Username has been sent to your %s';
    public const string REQUEST_LOGOUT = 'success_You have been successfully logged out';
    public const string REQUEST_ACTIVATE = 'success_Activation successful, you can now log in';
    public const string REQUEST_PERMISSION = 'success_Account permissions updated';
    public const string REQUEST_DELETE = 'success_deleted %s';

    // Warning Messages
    public const string ARTICLE_ALREADY_EXISTS = 'warning_Page %s already exists.<a href="/update/%s/%s">update</a>';
    public const string ARTICLE_DOES_NOT_EXIST = 'warning_Page %s does not exist.<a href="/create/%s/%s">create</a>';
    public const string EMPTY_ARTICLE = 'warning_Page %s is empty to remove it. Use <a href="/delete/%s/%s">delete</a>';
    public const string USER_LOGGED = 'warning_You cannot access the reset page while logged in';

    // Danger Messages
    public const string VALID_CSRF_ERROR = 'danger_CSRF validation failed';
    public const string VALID_PASSWORD_REGEX = 'danger_Password need lowercase. uppercase, number, special character';
    public const string VALID_EMAIL_FORMAT = 'danger_Invalid email format (%s)';
    public const string VALID_FORGOTTEN_USER = 'danger_Email (%s) does not exist in the database';
    public const string VALID_REG_CHECKBOX_FAIL = 'danger_Checkbox validation failed';
    public const string VALID_USER_NOT_EXIST = 'danger_User %s does not exist';
    public const string VALID_USER_ALREADY_EXISTS = 'danger_User %s already exists';
    public const string VALID_LEN_PASSWORD = 'danger_Password must be at least 6 characters long';
    public const string VALID_PASSWORD_AGAIN = 'danger_Passwords must match';
    public const string VALID_LEN_USER = 'danger_Username %s must be at least 4 characters long';
    public const string VALID_ACTIVE_MEMBER = 'danger_Account is not activated';
    public const string INVALID_URL = 'danger_Invalid URL, please check your email';
    public const string USER_NOT_LOGGED = 'danger_You must be logged in to view this page';
    public const string USER_PERMISSION = 'danger_You do not have permission to view this page';
    public const string AVATAR_UPLOAD = 'danger_Avatar must be uploaded';
    public const string AVATAR_SIZE = 'danger_Avatar must not exceed 5MB';
    public const string AVATAR_MIME_TYPE = 'danger_File must be PNG or JPG';
    public const string REQUEST_ACTIVATE_FAIL = 'danger_Account activation failed, please contact support';
}
