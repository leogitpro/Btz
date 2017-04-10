<?php
/**
 * FeedbackForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class FeedbackForm extends BaseForm
{

    /**
     * 表单: 反馈内容
     */
    private function addFeedbackContent()
    {
        $validators = [
            Factory::StringLength(10, 4096),
        ];

        $this->addTextareaElement('content', true, $validators);
    }


    public function addElements()
    {
        $this->addFeedbackContent();
    }

}