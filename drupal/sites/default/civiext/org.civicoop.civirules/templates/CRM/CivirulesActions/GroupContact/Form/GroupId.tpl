<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-group-contact">
    <div class="crm-section">
        <div class="label">{$form.type.label}</div>
        <div class="content">{$form.type.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section groups-single">
        <div class="label">{$form.group_id.label}</div>
        <div class="content">{$form.group_id.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section groups-multiple" style="display: none;">
        <div class="label">{$form.group_ids.label}</div>
        <div class="content">{$form.group_ids.html}</div>
        <div class="clear"></div>
    </div>
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">
    cj(function() {
        cj('select#type').change(triggerTypeChange);

        triggerTypeChange();
    });

    function triggerTypeChange() {
        cj('.groups-multiple').css('display', 'none');
        cj('.groups-single').css('display', 'none');
        var val = cj('#type').val();
        if (val == 0 ) {
            cj('.groups-single').css('display', 'block');
        } else {
            cj('.groups-multiple').css('display', 'block');
        }
    }
</script>

{/literal}