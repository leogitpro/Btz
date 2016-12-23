<?php
/**
 * Mail content template configuration
 *
 * User: leo
 */


return [
    // Template for active sign up user account mail content
    'active' => '
Hi: %username%

Please active your account by the follow code:
%active_code%
Or click the follow link address:
%active_link%

Thanks!
    ',

    // Template for activated user mail content.
    'activated' => '
Welcome: %username%!

Thanks join us
Click the follow link to quick login:
%login_link%

Thanks!
    ',

    'reset-password' => '
Hi: %username%!

To reset your password use the below url:
%reset_link%
The link will be expired in next %expired_hours% hours.

Thanks!
    ',
    // ...
];