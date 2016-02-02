<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="top"}
</div>

{if $action eq 8}
    {* Are you sure to delete form *}
    <h3>{ts}Delete rule{/ts}</h3>
    <div class="crm-block crm-form-block crm-civirule-rule_label-block">
        <div class="crm-section">{ts 1=$rule->label}Are you sure to delete rule '%1'?{/ts}</div>
    </div>
{else}
    {include file="CRM/Civirules/Form/RuleBlocks/RuleBlock.tpl"}
    {include file="CRM/Civirules/Form/RuleBlocks/TriggerBlock.tpl"}
    {if $action ne 1}
      {include file="CRM/Civirules/Form/RuleBlocks/ConditionBlock.tpl"}
      {include file="CRM/Civirules/Form/RuleBlocks/ActionBlock.tpl"}
    {/if}
{/if}
  
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
