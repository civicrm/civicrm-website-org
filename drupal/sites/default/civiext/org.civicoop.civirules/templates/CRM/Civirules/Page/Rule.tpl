<div class="crm-content-block crm-block">
  <div id="help">
    The existing CiviRules are listed below. You can manage, delete, disable/enable or add a rule. 
  </div>
  <div class="action-link">
    <a class="button new-option" href="{$add_url}">
      <span><div class="icon add-icon"></div>{ts}Add CiviRule{/ts}</span>
    </a>
  </div>
  <div id="civirule_wrapper" class="dataTables_wrapper">
    <table id="civirule-table" class="display">
      <thead>
        <tr>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Name{/ts}</th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Trigger{/ts}</th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Active{/ts}</th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Date Created{/ts}</th>
          <th class="sorting-disabled" rowspan="1" colspan="1">{ts}Created By{/ts}</th>
          <th class="sorting_disabled" rowspan="1" colspan="1"></th>
        </tr>
      </thead>
      <tbody>
        {assign var="row_class" value="odd-row"}
          {foreach from=$rules key=rule_id item=rule}
          <tr id="row1" class={$row_class}>
            <td>{$rule.label}</td>
            <td>{$rule.trigger_label}</td>
            <td>{$rule.is_active}</td>
            <td>{$rule.created_date|crmDate}</td>
            <td>{$rule.created_contact_name}</td>
            <td>
              <span>
                {foreach from=$rule.actions item=action_link}
                  {$action_link}
                {/foreach}
              </span>
            </td>
          </tr>
          {if $row_class eq "odd-row"}
            {assign var="row_class" value="even-row"}
          {else}
            {assign var="row_class" value="odd-row"}                        
          {/if}
        {/foreach}
      </tbody>
    </table>    
  </div>
  <div class="action-link">
    <a class="button new-option" href="{$add_url}">
      <span><div class="icon add-icon"></div>{ts}Add CiviRule{/ts}</span>
    </a>
  </div>
</div>
