<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-contact_subtype">
    <div class="crm-section">
        <div class="label">{$form.type.label}</div>
        <div class="content">{$form.type.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section sub_type-single">
        <div class="label">{$form.subtype.label}</div>
        <div class="content">{$form.subtype.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section sub_type-multiple" style="display: none;">
        <div class="label">{$form.subtypes.label}</div>
        <div class="content">{$form.subtypes.html}</div>
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
            cj('.sub_type-multiple').css('display', 'none');
            cj('.sub_type-single').css('display', 'none');
            var val = cj('#type').val();
            if (val == 0 ) {
                cj('.sub_type-single').css('display', 'block');
            } else {
                cj('.sub_type-multiple').css('display', 'block');
            }
        }
    </script>

{/literal}