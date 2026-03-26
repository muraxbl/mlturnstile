<form method="post">
  <div class="panel">
    <h3>Configuración Turnstile</h3>

    <div class="form-group">
      <label>Site Key</label>
      <input type="text" name="TURNSTILE_SITE_KEY" value="{$site_key}" class="form-control">
    </div>

    <div class="form-group">
      <label>Secret Key</label>
      <input type="text" name="TURNSTILE_SECRET_KEY" value="{$secret_key}" class="form-control">
    </div>

    <button type="submit" name="submitTurnstile" class="btn btn-primary">
      Guardar
    </button>
  </div>
</form>