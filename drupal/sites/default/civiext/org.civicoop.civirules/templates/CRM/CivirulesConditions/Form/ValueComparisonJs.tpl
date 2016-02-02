{literal}
<script type="text/javascript">
    cj(function() {
       cj('#operator').change(function() {
           var val = cj('#operator').val();
           switch (val) {
               case 'is one of':
               case 'is not one of':
               case 'contains one of':
               case 'not contains one of':
               case 'contains all of':
               case 'not contains all of':
                   cj('#multi_value_parent').removeClass('hiddenElement');
                   cj('#value_parent').addClass('hiddenElement');
                   break;
               default:
                   cj('#multi_value_parent').addClass('hiddenElement');
                   cj('#value_parent').removeClass('hiddenElement');
                   break;
           }
       });
        cj('#operator').trigger('change');
    });

    var CRM_civirules_condition_form_initialOperator;

    function CRM_civirules_condition_form_updateOperator (options, multiple) {
        if (!CRM_civirules_condition_form_initialOperator) {
            CRM_civirules_condition_form_initialOperator = cj('#operator').val();
        }
        cj('#operator option').removeClass('hiddenElement');
        if (options.length) {
            cj('#operator option[value=">"').addClass('hiddenElement');
            cj('#operator option[value=">="').addClass('hiddenElement');
            cj('#operator option[value="<"').addClass('hiddenElement');
            cj('#operator option[value="<="').addClass('hiddenElement');
        }
        if (options.length && multiple) {
            cj('#operator option[value="="').addClass('hiddenElement');
            cj('#operator option[value="!="').addClass('hiddenElement');
            cj('#operator option[value="is one of"').addClass('hiddenElement');
            cj('#operator option[value="is not one of"').addClass('hiddenElement');
        } else {
            cj('#operator option[value="contains one of"').addClass('hiddenElement');
            cj('#operator option[value="not contains one of"').addClass('hiddenElement');
            cj('#operator option[value="contains all of"').addClass('hiddenElement');
            cj('#operator option[value="not contains all of"').addClass('hiddenElement');
        }
        if (cj('#operator option:selected').hasClass('hiddenElement')) {
            if (!cj('#operator option[value="'+CRM_civirules_condition_form_initialOperator+'"]').hasClass('hiddenElement')) {
                cj('#operator option[value="'+CRM_civirules_condition_form_initialOperator+'"]').attr('selected', 'selected');
            } else {
                cj('#operator option:not(.hiddenElement)').first().attr('selected', 'selected');
            }
            cj('#operator').trigger('change');
        }
    }

    function CRM_civirules_condition_form_resetOptions () {
        cj('#multi_value_options').html('');
        cj('#value_options').html('');
        cj('#multi_value_options').addClass('hiddenElement');
        cj('#multi_value_parent .content.textarea').removeClass('hiddenElement');
        cj('#value_options').addClass('hiddenElement');
        cj('#value').removeClass('hiddenElement');
    }

    function CRM_civirules_conidtion_form_updateOptionValues(options, multiple) {
        CRM_civirules_condition_form_resetOptions();
        CRM_civirules_condition_form_updateOperator(options, multiple);
        if (options && options.length > 0) {
            var select_options = '';
            var multi_select_options = '';

            var currentSelectedOptions = cj('#multi_value').val().match(/[^\r\n]+/g);
            var currentSelectedOption = cj('#value').val();
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

            cj('#value').val(selectedOption);
            cj('#value').addClass('hiddenElement');
            cj('#value_options').html(select_options);
            cj('#value_options').removeClass('hiddenElement');
            cj('#value_options').change(function() {
                var value = cj(this).val();
                cj('#value').val(value);
            });

            cj('#multi_value').val(selectedOptions.join('\r\n'));
            cj('#multi_value_parent .content.textarea').addClass('hiddenElement');
            cj('#multi_value_options').html(multi_select_options);
            cj('#multi_value_options').removeClass('hiddenElement');
            cj('#multi_value_options input[type="checkbox"]').change(function() {
                var currentOptions = cj('#multi_value').val().match(/[^\r\n]+/g);
                if (!currentOptions) {
                    currentOptions = new Array();
                }
                var value = cj(this).val();
                var index = currentOptions.indexOf(value);
                if (this.checked) {
                    if (index < 0) {
                        currentOptions[currentOptions.length] = value;
                        cj('#multi_value').val(currentOptions.join('\r\n'));
                    }
                } else {
                    if (index >= 0) {
                        currentOptions.splice(index, 1);
                        cj('#multi_value').val(currentOptions.join('\r\n'));
                    }
                }
            });
        } else {
            cj('#multi_value_parent .content.textarea').removeClass('hiddenElement');
            cj('#value').removeClass('hiddenElement');
        }
    }

</script>
{/literal}