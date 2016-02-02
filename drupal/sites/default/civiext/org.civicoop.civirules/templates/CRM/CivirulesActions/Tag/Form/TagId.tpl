<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-group-contact">
    <div class="crm-section">
        <div class="label">{$form.type.label}</div>
        <div class="content">{$form.type.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section tags-single">
        <div class="label">{$form.tag_id.label}</div>
        <div class="content">{$form.tag_id.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section tags-multiple" style="display: none;">
        <div class="label">{$form.tag_ids.label}</div>
        <div class="content">{$form.tag_ids.html}</div>
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
        cj('.tags-multiple').css('display', 'none');
        cj('.tags-single').css('display', 'none');
        var val = cj('#type').val();
        if (val == 0 ) {
            cj('.tags-single').css('display', 'block');
        } else {
            cj('.tags-multiple').css('display', 'block');
        }
    }
</script>

{/literal}