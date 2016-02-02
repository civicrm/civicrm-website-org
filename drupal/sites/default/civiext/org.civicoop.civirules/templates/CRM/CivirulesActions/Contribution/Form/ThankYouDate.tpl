<h3>{$ruleActionHeader}</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block-contribution-thank-you-date">
  <div id="thank-you-radio-block" class="crm-section">
    <div class="label">{$form.thank_you_radio.label}</div>
    <div class="content">{$form.thank_you_radio.html}</div>
    <div class="clear"></div>
  </div>
  <div id="number_of_days-block" class="crm-section">
    <div class="label">{$form.number_of_days.label}</div>
    <div class="content">{$form.number_of_days.html}</div>
    <div class="clear"></div>
  </div>
  <div id ="thank_you_date-block" class="crm-section">
    <div class="label">{$form.thank_you_date.label}</div>
    <div class="content">{include file="CRM/common/jcalendar.tpl" elementName=thank_you_date}</div>
    <div class="clear"></div>
  </div>
</div>
<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
{literal}
  <script type="text/javascript">
    cj(function($) {
      $("input[type=radio][checked]").each(function() {
        if ($('#CIVICRM_QFID_0_thank_you_radio').prop('checked')) {
          $('#number_of_days-block').hide();
          $('#thank_you_date-block').hide();
        }
        if ($('#CIVICRM_QFID_1_thank_you_radio').prop('checked')) {
          $('#number_of_days-block').show();
          $('#thank_you_date-block').hide();
        }
        if ($('#CIVICRM_QFID_2_thank_you_radio').prop('checked')) {
          $('#number_of_days-block').hide();
          $('#thank_you_date-block').show();
        }
      });
      $('#CIVICRM_QFID_0_thank_you_radio').click(function() {
        $('#number_of_days-block').hide();
        $('#thank_you_date-block').hide();
      })
      $('#CIVICRM_QFID_1_thank_you_radio').click(function() {
        $('#number_of_days-block').show();
        $('#thank_you_date-block').hide();
      })
      $('#CIVICRM_QFID_2_thank_you_radio').click(function() {
        $('#number_of_days-block').hide();
        $('#thank_you_date-block').show();
      })
    });
  </script>
{/literal}