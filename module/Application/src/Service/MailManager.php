<?php
/**
 * Mail support
 *
 * User: leo
 */

namespace Application\Service;


use Zend\Mail\Message;
use Zend\Mail\Transport\Exception\RuntimeException;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;


class MailManager
{

    /**
     * @var array
     */
    private $config;

    /**
     * @var AppLogger
     */
    private $logger;


    /**
     * MailManager constructor.
     *
     * @param array $config
     * @param AppLogger $logger
     */
    public function __construct($config = array(), AppLogger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }


    /**
     * Send a e-mail
     *
     * @param string $recipient
     * @param string $subject
     * @param string $content
     * @return bool
     */
    public function sendMail($recipient, $subject, $content)
    {

        $result = false;

        if (!isset($this->config['smtp'])) {
            $this->logger->err('无 SMTP 配置, 不能实现发送邮件功能.');
            return $result;
        }

        try {

            $sender = @$this->config['smtp']['connection_config']['username'];

            $smtp = new Smtp();
            $option = new SmtpOptions($this->config['smtp']);
            $smtp->setOptions($option);

            $message = new Message();
            $message->addTo($recipient);
            $message->setFrom($sender, ucfirst(substr($sender, 0, (stripos($sender, '@')))));
            $message->setSubject($subject);
            $message->setBody($content);

            $smtp->send($message);

            $result = true;

        } catch (RuntimeException $e) {
            $this->logger->err('邮件发送失败: ' . PHP_EOL . $e->getMessage());
        }

        return $result;
    }

}