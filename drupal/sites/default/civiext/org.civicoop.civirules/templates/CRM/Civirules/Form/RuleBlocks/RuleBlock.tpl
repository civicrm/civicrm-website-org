{* block for rule data *}
<h3>Rule Details</h3>
<div class="crm-block crm-form-block crm-civirule-rule_label-block">
  <div class="crm-section">
    <div class="label">{$form.rule_label.label}</div>
    <div class="content">{$form.rule_label.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.rule_is_active.label}</div>
    <div class="content">{$form.rule_is_active.html}</div>
    <div class="clear"></div>
  </div>      
  <div class="crm-section">
    <div class="label">{$form.rule_created_date.label}</div>
    <div class="content">{$form.rule_created_date.value}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.rule_created_contact.label}</div>
    <div class="content">{$form.rule_created_contact.value}</div>
    <div class="clear"></div>
  </div>
  {$postRuleBlock}
</div>
