<?php
/**
 * module.config.mail_tpl.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

$MAIL_TPL_CONTACT = <<<EOF
Hi:
    Master!
    
There is a new contact from the E-mail: %email%.

%message%

Message post time: %datetime%.
Thanks!

EOF;



return [
    'contact' => $MAIL_TPL_CONTACT,
];