<?php
/**
 * Email address is existed validator
 *
 * User: leo
 */

namespace User\Validator;


use Zend\Validator\AbstractValidator;

class EmailExistedValidator extends AbstractValidator
{

    /**
     * @var array
     */
    protected $options = [
        'userManager' => null,
    ];


    // Message IDs
    const EMAIL_NO_EXISTS = 'emailNoExist';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::EMAIL_NO_EXISTS => 'The E-mail is exists other world!',
    ];


    /**
     * EmailExistedValidator constructor.
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['userManager'])) {
                $this->options['userManager'] = $options['userManager'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the email is existed.
     * if email has existed return true. else return false.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $userManager = $this->options['userManager'];
        if(null == $userManager) {
            $this->error(self::EMAIL_NO_EXISTS);
            return false;
        }

        $user = $userManager->getUserByEmail($value);

        if (null == $user) {
            $this->error(self::EMAIL_NO_EXISTS);
            return false;
        }

        return true;
    }

}