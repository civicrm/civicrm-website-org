<div class="crm-download-title">CiviCRM <?php echo $content['civicrm_version']; ?></div>
<div class='crm-support'>
  <div class='crm-introduction'>
    The latest stable version of CiviCRM is <?php echo $content['civicrm_version']; ?>. Select a download based on the content management software (CMS) you are using.
  </div>

  <div class="crm-download-listing">
    <?php foreach ($content['download_urls'] as $key => $values) { ?>
      <div class="crm-download-buttons">
        <?php
        $url = $values['url'];
        if (arg(1) == 'list' && variable_get('cividownload_mode') == 2 ) {
          $url = "https://download.civicrm.org/civicrm-{$content['civicrm_version']}-" . $values['filename'] . "?src=donate";
        }
        ?>
        <a class="download-link" href="<?php echo $url;?>">Download</a>
        &nbsp;CiviCRM <?php echo $content['civicrm_version']; ?> <?php echo 'for ' . $values['title']; ?>
      </div>
      <br/>
    <?php } ?>
  </div>
</div>
  <!-- LTS section -->

<div class="crm-download-title">CiviCRM <?php echo $content['civicrm_lts_version']; ?> LTS</div>
<div class="crm-support">
  <div class="crm-introduction">
  The current Long Term Support (LTS) release CiviCRM is <?php echo $content['civicrm_lts_version']; ?>. Select a download based on the content management software (CMS) you are using.
  </div>
  <div class="crm-download-listing">
  <?php foreach ($content['download_urls'] as $key => $values) { ?>
    <div class="crm-download-buttons">
<?php
$url = $values['url'];
if (arg(1) == 'list' && variable_get('cividownload_mode') == 2 ) {
$url = "https://download.civicrm.org/civicrm-{$content['civicrm_lts_version']}-" . $values['filename'] . "?src=donate";
}
?>
      <a class="download-link" href="<?php echo $url;?>&rtype=lts">Download</a>
      &nbsp;CiviCRM <?php echo $content['civicrm_lts_version']; ?> <?php echo 'for ' . $values['title']; ?>
    </div>
    <br/>
  <?php } ?>
  </div>
</div>
<!-- Resources section -->
  <div class="crm-support" style="border: 1px solid grey; padding: 10px;">
    <div class="crm-introduction" style="font-size: 1.5em;">Resources</div>
    <div class="crm-introduction" style="font-size: 1.2em;">
      <a href="https://civicrm.org/versions" target="_blank">Which version should I get? ›</a>
      <br />
      <a href="http://sourceforge.net/projects/civicrm/files/" target="_blank">Localization files, previous and pre-release versions (on Sourceforge.net) ›</a>
      <br />
      <a href="http://wiki.civicrm.org/confluence/display/CRMDOC/Installation+and+Upgrades" target="_blank">Installation and Upgrade Guides ›</a>
      <br />
      <a href="http://book.civicrm.org/user" target="_blank">User and Administrator Guide ›</a>
      <br />
      <a href="http://wiki.civicrm.org/confluence/display/CRMDOC/Develop" target="_blank">Developer Documentation ›</a>
    </div>
  </div>
  <br />

<!-- Hosting link section -->
<div style="background-color: #7EC757; padding: 10px; color: white; font-weight: bold; font-size: 18px; width: 90%">
  OR ... <a href="https://civicrm.org/providers/hosting" target="_blank">start using CiviCRM on demand &quot;in the cloud&quot; by signing up with one of our Hosting Providers.</a>
</div>