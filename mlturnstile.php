<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class mlTurnstile extends Module
{
    public function __construct()
    {
        $this->name = 'mlturnstile';
        $this->version = '1.2.0';
        $this->author = 'LC';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Turnstile Captcha (contactform)';
        $this->description = 'Protege el formulario de contacto con Cloudflare Turnstile';
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayContactExtra') &&
            $this->registerHook('actionContactFormSubmitBefore') &&
            // DEFAULT STYLES
            Configuration::updateValue('TURNSTILE_WIDTH', 'auto') &&
            Configuration::updateValue('TURNSTILE_DISPLAY', 'flex') &&
            Configuration::updateValue('TURNSTILE_POSITION', 'relative') &&
            Configuration::updateValue('TURNSTILE_ALIGN', 'flex-end') &&
            Configuration::updateValue('TURNSTILE_TOP', '') &&
            Configuration::updateValue('TURNSTILE_RIGHT', '') &&
            Configuration::updateValue('TURNSTILE_BOTTOM', '') &&
            Configuration::updateValue('TURNSTILE_LEFT', '');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitTurnstile')) {
            Configuration::updateValue('TURNSTILE_SITE_KEY', Tools::getValue('TURNSTILE_SITE_KEY'));
            Configuration::updateValue('TURNSTILE_SECRET_KEY', Tools::getValue('TURNSTILE_SECRET_KEY'));
            Configuration::updateValue('TURNSTILE_WIDTH', Tools::getValue('TURNSTILE_WIDTH'));
            Configuration::updateValue('TURNSTILE_DISPLAY', Tools::getValue('TURNSTILE_DISPLAY'));
            Configuration::updateValue('TURNSTILE_POSITION', Tools::getValue('TURNSTILE_POSITION'));
            Configuration::updateValue('TURNSTILE_ALIGN', Tools::getValue('TURNSTILE_ALIGN'));
            Configuration::updateValue('TURNSTILE_TOP', Tools::getValue('TURNSTILE_TOP'));
            Configuration::updateValue('TURNSTILE_RIGHT', Tools::getValue('TURNSTILE_RIGHT'));
            Configuration::updateValue('TURNSTILE_BOTTOM', Tools::getValue('TURNSTILE_BOTTOM'));
            Configuration::updateValue('TURNSTILE_LEFT', Tools::getValue('TURNSTILE_LEFT'));
        }

        $this->context->smarty->assign([
            'site_key' => Configuration::get('TURNSTILE_SITE_KEY'),
            'secret_key' => Configuration::get('TURNSTILE_SECRET_KEY'),
            'width' => Configuration::get('TURNSTILE_WIDTH'),
            'display' => Configuration::get('TURNSTILE_DISPLAY'),
            'position' => Configuration::get('TURNSTILE_POSITION'),
            'align' => Configuration::get('TURNSTILE_ALIGN'),
            'turnstile_top' => Configuration::get('TURNSTILE_TOP'),
            'turnstile_right' => Configuration::get('TURNSTILE_RIGHT'),
            'turnstile_bottom' => Configuration::get('TURNSTILE_BOTTOM'),
            'turnstile_left' => Configuration::get('TURNSTILE_LEFT'),

        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    // FRONT - mostrar widget. Añadir el hook {hook h='displayContactFormExtra'} en el template contact-form.tpl.
    public function hookDisplayContactExtra($params)
    {

        $this->context->smarty->assign([
            'turnstile_site_key' => Configuration::get(
                'TURNSTILE_SITE_KEY',
                null,
                null,
                $this->context->shop->id // clave por tienda
            ),
            'turnstile_width' => Configuration::get('TURNSTILE_WIDTH'),
            'turnstile_position' => Configuration::get('TURNSTILE_POSITION'),
            'turnstile_align' => Configuration::get('TURNSTILE_ALIGN'),
            'turnstile_top' => Configuration::get('TURNSTILE_TOP'),
            'turnstile_display' => Configuration::get('TURNSTILE_DISPLAY'),
            'turnstile_right' => Configuration::get('TURNSTILE_RIGHT'),
            'turnstile_bottom' => Configuration::get('TURNSTILE_BOTTOM'),
            'turnstile_left' => Configuration::get('TURNSTILE_LEFT'),
            'honeypot_name' => 'hp_' . md5(_COOKIE_KEY_ . 'honeypot') // nombre dinámico para el campo honeypot
        ]);

        return $this->display(__FILE__, 'views/templates/hook/turnstile.tpl');
    }

    // BACK - validar captcha
    public function hookActionContactFormSubmitBefore($params)
    {
        PrestaShopLogger::addLog('HOOK EJECUTADO', 1);
        // HONEYPOT CHECK
        $honeypotKey = 'hp_' . md5(_COOKIE_KEY_ . 'honeypot');
        $honeypot = Tools::getValue($honeypotKey);

        if (!empty($honeypot)) {
            // Opcional: log interno
            PrestaShopLogger::addLog('Honeypot triggered: bot detected', 1);

            $this->context->controller->errors[] = $this->trans(
                'Error al enviar el formulario',
                [],
                'Modules.Mlturnstile.Shop'
            );
            return;
        }

        // TURNSTILE
        $token = Tools::getValue('cf-turnstile-response');

        if (!$token) {
            $this->context->controller->errors[] = $this->trans(
                'Captcha is required',
                [],
                'Modules.Mlturnstile.Shop'
            );

            return;
        }

        $secret = Configuration::get(
            'TURNSTILE_SECRET_KEY',
            null,
            null,
            $this->context->shop->id
        );

        $response = Tools::file_get_contents(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            false,
            stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query([
                        'secret' => $secret,
                        'response' => $token,
                        'remoteip' => $_SERVER['REMOTE_ADDR'],
                    ]),
                ]
            ])
        );
        if (!$response) {
            PrestaShopLogger::addLog('Turnstile API failed', 3);
            $this->context->controller->errors[] = $this->trans(
                'Captcha verification failed',
                [],
                'Modules.Mlturnstile.Shop'
            );
            return;
        }
        $result = json_decode($response, true);
        PrestaShopLogger::addLog('Turnstile result: ' . json_encode($result), 1);
        if (empty($result) || empty($result['success']) || $result['success'] !== true) {
            $this->context->controller->errors[] = $this->trans(
                'Invalid captcha',
                [],
                'Modules.Mlturnstile.Shop'
            );
            return;
        }
    }
}