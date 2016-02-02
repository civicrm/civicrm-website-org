<h3>{$ruleConditionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_condition-block-contribution_distinctcontributingday">
  {include file="CRM/CivirulesConditions/Form/Utils/Period.tpl"}
  <div class="crm-section">
    <div class="label">{$form.operator.label}</div>
    <div class="content">{$form.operator.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.no_of_days.label}</div>
    <div class="content">{$form.no_of_days.html}</div>
    <div class="clear"></div>
  </div>

</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>