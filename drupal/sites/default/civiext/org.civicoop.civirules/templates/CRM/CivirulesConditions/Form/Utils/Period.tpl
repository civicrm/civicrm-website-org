<div class="crm-section">
    <div class="label">{$form.period.label}</div>
    <div class="content">{$form.period.html}
        <div class="period_replacements">
            {foreach from=$period_replacements item=replacement key=replacement_key}
                <div class="replacement hiddenElement" id="period_replacement_{$replacement_key}">
                    {$form.$replacement_key.html}
                    <span class="suffix"></span>
                </div>
            {/foreach}
        </div>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    var period_replacements = {$period_replacements_by_period};
    {literal}

    cj(function() {
        cj('select#period').change(triggerPeriodChange);

        triggerPeriodChange();
    });

    function triggerPeriodChange() {
        cj('.period_replacements .replacement').addClass('hiddenElement');
        var val = cj('#period').val();
        if (val) {
            cj(period_replacements[val]).each(function(index, element) {
                cj('#period_replacement_'+element.name).removeClass('hiddenElement');
                cj('#period_replacement_'+element.name+' .suffix').html(element.suffix);
            });
        }
    }
    {/literal}
</script>
