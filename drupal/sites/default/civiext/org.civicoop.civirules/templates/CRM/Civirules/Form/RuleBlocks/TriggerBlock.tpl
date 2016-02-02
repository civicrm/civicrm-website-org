{* block for linked trigger *}
<h3>Linked Trigger</h3>
<div class="crm-block crm-form-block crm-civirule-trigger-block">
  {if empty($form.rule_trigger_label.value)}
    <div class="crm-section">
      <div class="label">{$form.rule_trigger_select.label}</div>
      <div class="content">{$form.rule_trigger_select.html}</div>
      <div class="clear"></div>
    </div>
  {else}
    <div class="crm-section">
      <div id="civirule_triggerBlock-wrapper" class="dataTables_wrapper">
        <table id="civirule-triggerBlock-table" class="display">
          <tbody>
            <tr class="odd-row">
              <td>
                  {$form.rule_trigger_label.value}
                  {if $triggerClass && $triggerClass->getTriggerDescription()}
                    <br><span class="description">
                        {$triggerClass->getTriggerDescription()}
                    </span>
                  {/if}
                  {if $trigger_edit_params}
                      <br><a href="{$trigger_edit_params}">{ts}Edit trigger parameters{/ts}</a>
                  {/if}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  {/if}
</div>

