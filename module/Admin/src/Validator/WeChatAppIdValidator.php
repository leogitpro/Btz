<?php
/**
 * WeChatAppIdValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Validator;


use Admin\WeChat\Exception\InvalidArgumentException;
use Admin\WeChat\Remote;
use Zend\Validator\AbstractValidator;


/**
 * 微信 AppID 验证器
 *
 * Class WeChatAppIdValidator
 * @package Admin\Validator
 */
class WeChatAppIdValidator extends AbstractValidator
{

    //验证失败类型
    const APP_ID_INVALID = 'appIdInvalid';

    //验证器配置项
    private $options = [
        'weChatRemote' => null,
    ];

    //验证失败提示语
    protected $messageTemplates = [
        self::APP_ID_INVALID => '无效的微信 AppID',
    ];


    /**
     * WeChatAppIdValidator constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['weChatRemote'])) {
                $this->options['weChatRemote'] = $options['weChatRemote'];
            }
        }

        parent::__construct($options);
    }


    /**
     * 验证规则
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (empty($value)) {
            $this->error(self::APP_ID_INVALID);
            return false;
        }

        $weChatRemote = $this->options['weChatRemote'];
        if (null == $weChatRemote || !$weChatRemote instanceof Remote) {
            $this->error(self::APP_ID_INVALID);
            return false;
        }

        try {
            $res = $weChatRemote->getAccessToken($value, '9f6946ceu82c40f57f744933192c9910');
        } catch (InvalidArgumentException $e) {
            $weChatRemote->getLogger()->excaption($e);
        }

        return false;
    }

}