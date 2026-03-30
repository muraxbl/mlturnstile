<?php
class ContactformOverride extends Contactform
{
    public function sendMessage()
    {
        $module = Module::getInstanceByName('mlturnstile');
        if ($module && $module->active) {
            $module->hookActionContactFormSubmitBefore([]);
        }
        if (!count($this->context->controller->errors)) {
            return parent::sendMessage();
        }
    }
}
