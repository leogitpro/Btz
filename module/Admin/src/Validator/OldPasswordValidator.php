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
        'adminerManager' => null,
        'authService' => null,
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
            if (isset($options['adminerManager'])) {
                $this->options['adminerManager'] = $options['adminerManager'];
            }
            if (isset($options['authService'])) {
                $this->options['authService'] = $options['authService'];
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
        $adminerManager = $this->options['adminerManager'];
        $authService = $this->options['authService'];

        $adminer = $adminerManager->getAdministrator($authService->getIdentity());
        if (null == $adminer) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        $password = $adminer->getAdminPasswd();
        if (md5($value) != $password) {
            $this->error(self::OLD_PWD_INVALID);
            return false;
        }

        return true;
    }


}