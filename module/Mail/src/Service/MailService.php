<?php
/**
 * MailService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Mail\Service;


use Mail\Exception\RuntimeException;
use Mail\Exception\InvalidArgumentException;
use Zend\Mail\Message;
use Zend\Mail\Transport\Exception\RuntimeException as TransportRuntimeException;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;


class MailService
{

    /**
     * @var array
     */
    private $config;


    public function __construct($config)
    {
        $this->config = (array)$config;
    }


    /**
     * Send a e-mail
     *
     * @param string $recipient
     * @param string $subject
     * @param string $content
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function sendMail($recipient, $subject, $content)
    {
        $smtpConfig = @$this->config['smtp'];
        if (!is_array($smtpConfig) || empty($smtpConfig)) {
            throw new InvalidArgumentException('无 SMTP 配置, 无法发送邮件');
        }

        $sender = @$smtpConfig['connection_config']['username'];

        $smtp = new Smtp();
        $option = new SmtpOptions($smtpConfig);
        $smtp->setOptions($option);

        $mail = new Message();
        $mail->addTo($recipient);
        $mail->setSubject($subject);
        $mail->setBody($content);
        $mail->setFrom($sender, ucfirst(substr($sender, 0, (stripos($sender, '@')))));

        try {
            $smtp->send($mail);
        } catch (TransportRuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }

    }


}