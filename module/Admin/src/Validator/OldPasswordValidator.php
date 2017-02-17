<?php


namespace Admin\Validator;


use Zend\Validator\AbstractValidator;


class OldPasswordValidator extends AbstractValidator
{

    // Message IDs
    const OLD_PWD_INVALID = 'oldPwdInvalid';

    /**
     * Validator options
     *
     * @var array
     */
    private $options = [
        'memberManager' => null,
    ];


    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = [
        self::OLD_PWD_INVALID => 'The old password not matched.',
    ];


    /**
     * OldPasswordValidator constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['memberManager'])) {
                $this->options['memberManager'] = $options['memberManager'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Validate the value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $memberManager = $this->options['memberManager'];

        $member = $memberManager->getCurrentMember();
        if (null == $member) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        $password = $member->getMemberPassword();
        if (md5($value) != $password) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        return true;
    }


}