<?php
class ContactformOverride extends Contactform
{
    public function sendMessage()
    {
        // Obtener instancia del módulo
        $module = Module::getInstanceByName('mlturnstile');

        if ($module && $module->active) {
            // Ejecutar SOLO tu validación
            $module->hookActionContactFormSubmitBefore([]);
        }

        // Si hay errores → NO enviar
        if (!count($this->context->controller->errors)) {
            return parent::sendMessage();
        }
    }
}