{if $confirmed}
  {ts 1=$email 2=$group->title}The email address "%1" is unsubscribed from group "%2".{/ts}
{else}
  {ts}The confirmation code is invalid or expired.{/ts}
{/if}
