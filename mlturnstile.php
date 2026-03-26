<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class mlTurnstile extends Module
{
    public function __construct()
    {
        $this->name = 'mlturnstile';
        $this->version = '1.0.1';
        $this->author = 'LC';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Turnstile Captcha (contactform)';
        $this->description = 'Protege el formulario de contacto con Cloudflare Turnstile (multitienda)';
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayContactForm') &&
            $this->registerHook('actionContactFormSubmitBefore');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitTurnstile')) {
            Configuration::updateValue('TURNSTILE_SITE_KEY', Tools::getValue('TURNSTILE_SITE_KEY'));
            Configuration::updateValue('TURNSTILE_SECRET_KEY', Tools::getValue('TURNSTILE_SECRET_KEY'));
        }

        $this->context->smarty->assign([
            'site_key' => Configuration::get('TURNSTILE_SITE_KEY'),
            'secret_key' => Configuration::get('TURNSTILE_SECRET_KEY'),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    // 👇 FRONT - mostrar widget
    public function hookDisplayContactForm($params)
    {
        $this->context->controller->registerJavascript(
            'turnstile',
            'https://challenges.cloudflare.com/turnstile/v0/api.js',
            ['server' => 'remote', 'position' => 'head']
        );

        $this->context->smarty->assign([
            'turnstile_site_key' => Configuration::get(
                'TURNSTILE_SITE_KEY',
                null,
                null,
                $this->context->shop->id // 🔥 clave por tienda
            ),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/turnstile.tpl');
    }

    // 👇 BACK - validar captcha
    public function hookActionContactFormSubmitBefore($params)
    {
        // 🪤 HONEYPOT CHECK
        $honeypot = Tools::getValue('company_website');

        if (!empty($honeypot)) {
            // Opcional: log interno
            PrestaShopLogger::addLog('Honeypot triggered: bot detected', 1);

            $this->context->controller->errors[] = 'Error al enviar el formulario';
            return;
        }

        // 🔐 TURNSTILE
        $token = Tools::getValue('cf-turnstile-response');

        if (!$token) {
            $this->context->controller->errors[] = 'Captcha requerido';
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

        $result = json_decode($response, true);

        if (empty($result['success'])) {
            $this->context->controller->errors[] = 'Captcha inválido';
        }
    }
}