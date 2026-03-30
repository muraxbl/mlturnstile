<div class="form-group">
  <div class="cf-turnstile" style="
    display: {$turnstile_display};
    justify-content: {$turnstile_align};
    position: {$turnstile_position};
    {if $turnstile_top}top: {$turnstile_top};{/if}
    {if $turnstile_right}right: {$turnstile_right};{/if}
    {if $turnstile_bottom}bottom: {$turnstile_bottom};{/if}
    {if $turnstile_left}left: {$turnstile_left};{/if}"
    data-sitekey="{$turnstile_site_key}"></div>
</div>


{* Honeypot invisible *}
<div style="position:absolute; left:-9999px; top:-9999px;">
  <label>Demuestra que eres humano. Escribe algo aquí:</label>
  <input type="text" name="{$honeypot_name}" value="">
</div>

{* Cargamos el script de Turnstile solo en la página de contacto, para no afectar otras partes del sitio *}
<script>
(function() {
  function loadTurnstile() {
    if (window.turnstile || document.querySelector('script[data-turnstile]')) {
      return;
    }

    var s = document.createElement('script');
    s.src = "https://challenges.cloudflare.com/turnstile/v0/api.js";
    s.async = true;
    s.defer = true;
    s.setAttribute('data-turnstile', 'true');
    document.head.appendChild(s);
  }

  var observer = new MutationObserver(function() {
    if (document.querySelector('.cf-turnstile')) {
      loadTurnstile();
      observer.disconnect();
    }
  });

  observer.observe(document.body, { childList: true, subtree: true });

  // fallback inicial
  if (document.querySelector('.cf-turnstile')) {
    loadTurnstile();
  }
})();
</script>