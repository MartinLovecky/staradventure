<?php

namespace Mlkali\Sa\Support;

class Enum
{
    // %s = dynamic values, [ %d for integers, %f for floats, and %u for unsigned integers ]
    // use sprintf() only for %s ...

    // Success Messages
    const string ARTICLE_CREATED = 'success_Page %s created %s';
    const string ARTICLE_UPDATED = 'success_Page %s updated %s';
    const string ARTICLE_DELETED = 'success_Page %s deleted %s';
    const string REQUEST_REGISTER = 'success_Activation link has been sent to email %s';
    const string REQUEST_LOGIN = 'success_Welcome back %s';
    const string REQUEST_RESET_SEND = 'success_Password reset link has been sent to %s';
    const string REQUEST_RESET_PASSWORD = 'success_Password has been successfully changed';
    const string REQUEST_FORGOTTEN_USER = 'success_Username has been sent to your %s';
    const string REQUEST_LOGOUT = 'success_You have been successfully logged out';
    const string REQUEST_ACTIVATE = 'success_Activation successful, you can now log in';
    const string REQUEST_PERMISSION = 'success_Account permissions updated';
    const string REQUEST_DELETE = 'success_Account successfully deleted';

    // Warning Messages
    const string ARTICLE_ALREADY_EXISTS = 'warning_Page %s already exists. Use <a href="/update/%s/%s">update</a>';
    const string ARTICLE_DOES_NOT_EXIST = 'warning_Page %s does not exist. Use <a href="/create/%s/%s">create</a>';
    const string USER_LOGGED = 'warning_You cannot access the reset page while logged in';

    // Danger Messages
    const string VALIDATION_CSRF_ERROR = 'danger_CSRF validation failed';
    const string VALIDATION_PASSWORD_REGEX = 'danger_Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (!@$%^&)';
    const string VALIDATION_EMAIL_FORMAT = 'danger_Invalid email format (%s)';
    const string VALIDATION_FORGOTTEN_USER = 'danger_Email (%s) does not exist in the database';
    const string VALIDATION_REG_CHECKBOX_FAIL = 'danger_Checkbox validation failed';
    const string VALIDATION_USER_NOT_EXIST = 'danger_User %s does not exist';
    const string VALIDATION_USER_ALREADY_EXISTS = 'danger_User %s already exists';
    const string VALIDATION_LEN_PASSWORD = 'danger_Password must be at least 6 characters long';
    const string VALIDATION_PASSWORD_AGAIN = 'danger_Passwords must match';
    const string VALIDATION_LEN_USER = 'danger_Username %s must be at least 4 characters long';
    const string VALIDATION_ACTIVE_MEMBER = 'danger_Account is not activated';
    const string INVALID_URL = 'danger_Invalid URL, please check your email';
    const string USER_NOT_LOGGED = 'danger_You must be logged in to view this page';
    const string USER_PERMISSION = 'danger_You do not have permission to view this page';
    const string AVATAR_UPLOAD = 'danger_Avatar must be uploaded';
    const string AVATAR_SIZE = 'danger_Avatar must not exceed 5MB';
    const string AVATAR_MIME_TYPE = 'danger_File must be PNG or JPG';
    const string REQUEST_ACTIVATE_FAIL = 'danger_Account activation failed, please contact support';
}
