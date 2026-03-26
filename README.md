# Módulo mlturnstile

Este módulo integra una instancia de **Cloudflare Turnstile** para proteger el formulario de contacto original de PrestaShop (`contactform`). Su objetivo es mejorar la seguridad y prevenir el spam mediante la verificación de usuarios reales.

## Funcionalidades principales

- **Integración con Cloudflare Turnstile:** Añade protección al formulario de contacto utilizando el servicio Turnstile de Cloudflare.
- **Configuración sencilla:** Permite editar y configurar los valores de **Site Key** y **Secret Key** desde la administración del módulo.
- **Compatibilidad multitienda:** Funciona correctamente en contexto tienda, permitiendo configuraciones independientes por tienda si es necesario.
- **Enganche a hooks:** Se conecta a los hooks necesarios para asegurar la integración transparente con el formulario de contacto.

## Instalación

1. Sube el módulo a tu instalación de PrestaShop.
2. Actívalo desde el panel de administración.
3. Configura tus claves de Cloudflare Turnstile en la sección de configuración del módulo.

## Requisitos

- PrestaShop 1.7 o superior.
- Cuenta en [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) para obtener las claves necesarias.

## Soporte

Para dudas o soporte, puedes utilizar las herramientas de Github.
