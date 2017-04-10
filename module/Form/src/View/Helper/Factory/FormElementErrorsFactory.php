<?php
/**
 * FormElementErrorsFactory.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Form\View\Helper\Factory;



use Interop\Container\ContainerInterface;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\ServiceManager\Factory\FactoryInterface;


class FormElementErrorsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $helper = new FormElementErrors();
        $helper->setMessageOpenFormat('<p class="text-danger">');
        $helper->setMessageSeparatorString('</p><p class="text-danger">');
        $helper->setMessageCloseString('</p>');
        return $helper;
    }


}