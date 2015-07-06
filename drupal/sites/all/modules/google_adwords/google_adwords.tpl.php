<?php
/**
 * @file
 * Google AdWord template for page JS.
 */
?>
<!-- Google Code for Conversion Page //-->
<script language="JavaScript" type="text/javascript">
  /* <![CDATA[ */
  var google_conversion_id = <?php print $conversion_id; ?>;
  var google_conversion_language = "<?php print $conversion_language; ?>";
  var google_conversion_format = "<?php print $conversion_format; ?>";
  var google_conversion_color = "<?php print $conversion_color; ?>";
  var google_conversion_label = "<?php print $conversion_label; ?>";
  /* ]]> */
</script>
<script language="JavaScript" src="<?php print $google_js; ?>"></script>
<noscript>
<img height="1" width="1" border="0" src="https://www.googleadservices.com/pagead/conversion/<?php print $conversion_id; ?>/?label=<?php print $conversion_label; ?>&amp;guid=ON&amp;script=0"/>
</noscript>
