<form method="post" action="{$smarty.server.REQUEST_URI}">
    <div class="panel">
        <h3>{l s='Configuración Turnstile' mod='mlturnstile'}</h3>

        <div class="form-group">
            <label>{l s='Site Key' mod='mlturnstile'}</label>
            <input type="text" name="TURNSTILE_SITE_KEY" value="{$site_key}" class="form-control">
        </div>

        <div class="form-group">
            <label>{l s='Secret Key' mod='mlturnstile'}</label>
            <input type="text" name="TURNSTILE_SECRET_KEY" value="{$secret_key}" class="form-control">
        </div>

        <div class="form-group">
            <label>{l s='Width' mod='mlturnstile'}</label>
            <input type="text" name="TURNSTILE_WIDTH" value="{$width}" placeholder="300px o 100%" class="form-control">
        </div>

        <div class="form-group">
            <label>{l s='Position' mod='mlturnstile'}</label>
            <select name="TURNSTILE_POSITION" id="turnstile_position" class="form-control">
                <option value="relative" {if $position == 'relative'}selected{/if}>Relative</option>
                <option value="absolute" {if $position == 'absolute'}selected{/if}>Absolute</option>
            </select>
        </div>

        <div id="position_fields" style="display:none;">
            <div class="form-group">
                <label>Top</label>
                <input type="text" name="TURNSTILE_TOP" value="{$turnstile_top}" placeholder="10px" class="form-control">
            </div>
            <div class="form-group">
                <label>Right</label>
                <input type="text" name="TURNSTILE_RIGHT" value="{$turnstile_right}" class="form-control">
            </div>
            <div class="form-group">
                <label>Bottom</label>
                <input type="text" name="TURNSTILE_BOTTOM" value="{$turnstile_bottom}" class="form-control">
            </div>
            <div class="form-group">
                <label>Left</label>
                <input type="text" name="TURNSTILE_LEFT" value="{$turnstile_left}" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label>Display</label>
            <select name="TURNSTILE_DISPLAY" class="form-control">
                <option value="block" {if $display == 'block'}selected{/if}>Block</option>
                <option value="flex" {if $display == 'flex'}selected{/if}>Flex</option>
                <option value="grid" {if $display == 'grid'}selected{/if}>Grid</option>
                <option value="inline-block" {if $display == 'inline-block'}selected{/if}>Inline-block</option>
                <option value="inline-flex" {if $display == 'inline-flex'}selected{/if}>Inline-flex</option>
            </select>
        </div>

        <div class="form-group">
            <label>{l s='Justify Content (if display is flex)' mod='mlturnstile'}</label>
            <select name="TURNSTILE_ALIGN" class="form-control">
                <option value="flex-start" {if $align == 'flex-start'}selected{/if}>Left</option>
                <option value="center" {if $align == 'center'}selected{/if}>Center</option>
                <option value="flex-end" {if $align == 'flex-end'}selected{/if}>Right</option>
            </select>
        </div>

        <button type="submit" name="submitTurnstile" class="btn btn-primary">
            {l s='Guardar' mod='mlturnstile'}
        </button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var select = document.getElementById('turnstile_position');
  var fields = document.getElementById('position_fields');
  function toggleFields() {
    fields.style.display = (select.value === 'absolute') ? 'block' : 'none';
  }
  select.addEventListener('change', toggleFields);
  toggleFields();
});
</script>