
{*block for linked condition *}    
<h3>Linked Action(s)</h3>
<div class="crm-block crm-form-block crm-civirule-rule_action-block">
  <div class="crm-section">
    <div id="civirule_wrapper" class="dataTables_wrapper">
      <table id="civirule-table" class="display">
        <thead>
          <tr>
            <th>{ts}Name{/ts}</th>
            <th>{ts}Extra parameters{/ts}</th>
            <th class="nosort">&nbsp;</th>
            <th id="nosort">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          {assign var="rowClass" value="odd_row"}
          {assign var="rowNumber" value=1}
          {foreach from=$ruleActions key=action_id item=ruleAction}
            <tr id="row_{$rowNumber}" class={$rowClass}>
              <td hidden="1" id="ruleActionId">{$ruleAction.id}</td>
              <td>{$ruleAction.label}</td>
              {if !empty($ruleAction.formattedConditionParams)}
                <td>{$ruleAction.formattedConditionParams}</td>
              {else}
                <td>&nbsp;</td>
              {/if}
              <td>
                  {$ruleAction.formattedDelay}
              </td>
              <td>
                <span>
                  {foreach from=$ruleAction.actions item=actionLink}
                    {$actionLink}
                  {/foreach}
                </span>
              </td>
            </tr>
            {if $row_class eq "odd_row"}
              {assign var="rowClass" value="even-row"}
            {else}
              {assign var="row_class" value="odd-row"}
            {/if}
            {assign var="rowNumber" value=$rowNumber+1}
          {/foreach}
        </tbody>
      </table>
    </div>
  </div>
  <div class="crm-submit-buttons">
    <a class="add button" title="Add Action" href="{$ruleActionAddUrl}">
      <span><div class="icon add-icon"></div>Add Action</span></a>
  </div>
</div>

