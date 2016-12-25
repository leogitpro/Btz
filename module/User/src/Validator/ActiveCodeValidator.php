<?php
/**
 * Custom active code validator
 *
 * User: leo
 */

namespace User\Validator;


use User\Entity\User;
use Zend\Validator\AbstractValidator;

class ActiveCodeValidator extends AbstractValidator
{

    private $options = [
        'userManager' => null,
    ];


    // Message IDs
    const CODE_INVALID = 'codeInvalid';
    const CODE_USED = 'codeUsed';

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::CODE_INVALID => 'The code is invalid.',
        self::CODE_USED => 'The code is used activated',
    ];



    /**
     * ActiveCodeValidator constructor.
     * @param null $options
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
        $code = (string)$value;
        $userManager = $this->options['userManager'];
        $user = $userManager->getUserByActiveToken($code);

        if (null == $user) {
            $this->error(self::CODE_INVALID);
            return false;
        }

        if(User::STATUS_ACTIVE == $user->getStatus()) {
            $this->error(self::CODE_USED);
            return false;
        }

        return true;
    }



}