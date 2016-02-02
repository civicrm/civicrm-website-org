{* block for rule condition data *}
<h3>{$ruleConditionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_condition-block">
  {if $countRuleConditions > 0}
    <div class="crm-section">
      <div class="label">{$form.rule_condition_link_select.label}</div>
      <div class="content">{$form.rule_condition_link_select.html}</div>
      <div class="clear"></div>
    </div>
  {/if}
  <div class="crm-section">
    <div class="label">{$form.rule_condition_select.label}</div>
    <div class="content">{$form.rule_condition_select.html}</div>
    <div class="clear"></div>
  </div>
</div>
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
