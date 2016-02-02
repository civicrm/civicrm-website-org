<h3>{$ruleConditionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_condition-block-value-comparison">
    <div class="crm-section">
        <div class="label"></div>
        <div class="content"><h2>{ts}Original value{/ts}</h2></div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.original_operator.label}</div>
        <div class="content">{$form.original_operator.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section" id="original_value_parent">
        <div class="label">{$form.original_value.label}</div>
        <div class="content">
            {$form.original_value.html}
            <select id="original_value_options" class="hiddenElement">

            </select>
        </div>
        <div class="clear"></div>
    </div>
    <div class="crm-section" id="original_multi_value_parent">
        <div class="label">{$form.original_multi_value.label}</div>
        <div class="content textarea">
            {$form.original_multi_value.html}
            <p class="description">{ts}Seperate each value on a new line{/ts}</p>
        </div>
        <div id="original_multi_value_options" class="hiddenElement content">

        </div>
        <div class="clear"></div>
    </div>

    <div class="crm-section">
        <div class="label"></div>
        <div class="content"><h2>{ts}New value{/ts}</h2></div>
        <div class="clear"></div>
    </div>
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
    cj(function() {
        cj('#original_operator').change(function() {
            var val = cj('#original_operator').val();
            switch (val) {
                case 'is one of':
                case 'is not one of':
                case 'contains one of':
                case 'not contains one of':
                case 'contains all of':
                case 'not contains all of':
                    cj('#original_multi_value_parent').removeClass('hiddenElement');
                    cj('#original_value_parent').addClass('hiddenElement');
                    break;
                default:
                    cj('#original_multi_value_parent').addClass('hiddenElement');
                    cj('#original_value_parent').removeClass('hiddenElement');
                    break;
            }
        });
        cj('#original_operator').trigger('change');
    });

    var CRM_civirules_condition_form_initialOriginalOperator;

    function CRM_civirules_condition_form_updateOriginalOperator (options, multiple) {
        if (!CRM_civirules_condition_form_initialOriginalOperator) {
            CRM_civirules_condition_form_initialOriginalOperator = cj('#original_operator').val();
        }
        cj('#original_operator option').removeClass('hiddenElement');
        if (options.length) {
            cj('#original_operator option[value=">"').addClass('hiddenElement');
            cj('#original_operator option[value=">="').addClass('hiddenElement');
            cj('#original_operator option[value="<"').addClass('hiddenElement');
            cj('#original_operator option[value="<="').addClass('hiddenElement');
        }
        if (options.length && multiple) {
            cj('#original_operator option[value="="').addClass('hiddenElement');
            cj('#original_operator option[value="!="').addClass('hiddenElement');
            cj('#original_operator option[value="is one of"').addClass('hiddenElement');
            cj('#original_operator option[value="is not one of"').addClass('hiddenElement');
        } else {
            cj('#original_operator option[value="contains one of"').addClass('hiddenElement');
            cj('#original_operator option[value="not contains one of"').addClass('hiddenElement');
            cj('#original_operator option[value="contains all of"').addClass('hiddenElement');
            cj('#original_operator option[value="not contains all of"').addClass('hiddenElement');
        }
        if (cj('#original_operator option:selected').hasClass('hiddenElement')) {
            if (!cj('#original_operator option[value="'+CRM_civirules_condition_form_initialOriginalOperator+'"]').hasClass('hiddenElement')) {
                cj('#original_operator option[value="'+CRM_civirules_condition_form_initialOriginalOperator+'"]').attr('selected', 'selected');
            } else {
                cj('#original_operator option:not(.hiddenElement)').first().attr('selected', 'selected');
            }
            cj('#original_operator').trigger('change');
        }
    }

    function CRM_civirules_condition_form_resetOriginalOptions () {
        cj('#original_multi_value_options').html('');
        cj('#original_value_options').html('');
        cj('#original_multi_value_options').addClass('hiddenElement');
        cj('#original_multi_value_parent .content.textarea').removeClass('hiddenElement');
        cj('#original_value_options').addClass('hiddenElement');
        cj('#original_value').removeClass('hiddenElement');
    }

    function CRM_civirules_conidtion_form_updateOriginalOptionValues(options, multiple) {
        CRM_civirules_condition_form_resetOriginalOptions();
        CRM_civirules_condition_form_updateOriginalOperator(options, multiple);
        if (options && options.length > 0) {
            var select_options = '';
            var multi_select_options = '';

            var currentSelectedOptions = cj('#original_multi_value').val().match(/[^\r\n]+/g);
            var currentSelectedOption = cj('#original_value').val();
            var selectedOptions = new Array();
            var selectedOption = '';
            if (!currentSelectedOptions) {
                currentSelectedOptions = new Array();
            }

            for(var i=0; i < options.length; i++) {
                var selected = '';
                var checked = '';
                if (currentSelectedOptions.indexOf(options[i].key) >= 0) {
                    checked = 'checked="checked"';
                    selectedOptions[selectedOptions.length] = options[i].key;
                }
                if (options[i].key == currentSelectedOption || (!currentSelectedOption && i == 0)) {
                    selected='selected="selected"';
                    selectedOption = options[i].key;
                }
                multi_select_options = multi_select_options + '<input type="checkbox" value="'+options[i].key+'" '+checked+'>'+options[i].value+'<br>';
                select_options = select_options + '<option value="'+options[i].key+'" '+selected+'>'+options[i].value+'</option>';
            }

            cj('#original_value').val(selectedOption);
            cj('#original_value').addClass('hiddenElement');
            cj('#original_value_options').html(select_options);
            cj('#original_value_options').removeClass('hiddenElement');
            cj('#original_value_options').change(function() {
                var value = cj(this).val();
                cj('#value').val(value);
            });

            cj('#original_multi_value').val(selectedOptions.join('\r\n'));
            cj('#original_multi_value_parent .content.textarea').addClass('hiddenElement');
            cj('#original_multi_value_options').html(multi_select_options);
            cj('#original_multi_value_options').removeClass('hiddenElement');
            cj('#original_multi_value_options input[type="checkbox"]').change(function() {
                var currentOptions = cj('#original_multi_value').val().match(/[^\r\n]+/g);
                if (!currentOptions) {
                    currentOptions = new Array();
                }
                var value = cj(this).val();
                var index = currentOptions.indexOf(value);
                if (this.checked) {
                    if (index < 0) {
                        currentOptions[currentOptions.length] = value;
                        cj('#original_multi_value').val(currentOptions.join('\r\n'));
                    }
                } else {
                    if (index >= 0) {
                        currentOptions.splice(index, 1);
                        cj('#original_multi_value').val(currentOptions.join('\r\n'));
                    }
                }
            });
        } else {
            cj('#original_multi_value_parent .content.textarea').removeClass('hiddenElement');
            cj('#original_value').removeClass('hiddenElement');
        }
    }

    var options = new Array();
    {/literal}
    {if ($field_options)}
        {foreach from=$field_options item=value key=key}
    {literal}options[options.length] = {'key': {/literal}'{$key}', 'value': '{$value}'{literal}};{/literal}
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
        CRM_civirules_conidtion_form_updateOriginalOptionValues(options, multiple);
    });
</script>
{/literal}