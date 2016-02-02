<h3>{$ruleConditionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_condition-block-value-comparison">
    <div class="crm-section">
        <div class="label">{$form.operator.label}</div>
        <div class="content">{$form.operator.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section" id="value_parent">
        <div class="label">{$form.value.label}</div>
        <div class="content">
            {$form.value.html}
            <select id="value_options" class="hiddenElement">

            </select>
        </div>
        <div class="clear"></div>
    </div>
    <div class="crm-section" id="multi_value_parent">
        <div class="label">{$form.multi_value.label}</div>
        <div class="content textarea">
            {$form.multi_value.html}
            <p class="description">{ts}Seperate each value on a new line{/ts}</p>
        </div>
        <div id="multi_value_options" class="hiddenElement content">

        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{include file="CRM/CivirulesConditions/Form/ValueComparisonJs.tpl"}

{literal}
<script type="text/javascript">
    var options = new Array();
    {/literal}
    {if ($field_options)}
        {foreach from=$field_options item=value key=key}
    {literal}options[options.length] = {'key': key, 'value': value};{/literal}
        {/foreach}
    {/if}
    {if ($is_field_option_multiple)}
        var multiple = true;
    {else}
        var multiple = false;
    {/if}
    {literal}
    cj(function() {
        CRM_civirules_conidtion_form_updateOptionValues(options, multiple);
    });
</script>
{/literal}