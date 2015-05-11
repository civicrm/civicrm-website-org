<!-- Stable releases section -->
<div id="dl-stable">
	<?php $civicrm_version = variable_get('civicrm_stable_version', '4.5.2'); ?>
	<div id="dl-stable-top">
		<img src="/sites/all/themes/civicrm/css/images/download-big.png" width="234" height="230" alt="V" id="dl-arrow" />
		<h2 class="page-title">The latest stable version of CiviCRM is <?php echo $civicrm_version; ?>.</h2>
		<p>Select a download based on the content management software (CMS) you are using.</p>
	</div>
	<div id="dl-options">
		<?php foreach ($content['download_urls'] as $key => $values) { ?>
			<div class="dl-option <?php echo $values['cssclass']; ?>">
				<img src="/sites/all/themes/civicrm/css/images/cms-<?php echo $values['cms']; ?>.png" width="65" height="65" alt="CMS icon" />
				<div class="dl-option-inner">
					<h3><?php echo $values['cms']; ?></h3>
					<p class="dl-text"><?php echo 'Compatible with ' . $values['title']; ?>
					<p><a class="btn-dark" href="<?php echo $values['url'];?>">Download</a></p>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
  
  
<!-- LTS section -->
<div id="dl-lts">
	<?php $civicrm_lts_version = variable_get('civicrm_lts_version', '4.4.10'); ?>
	<h2 class="page-title">CiviCRM <?php echo $civicrm_lts_version; ?> LTS</h2>
	<p>The current Long Term Support (LTS) release CiviCRM is <?php echo $civicrm_lts_version; ?>.<br />Select a download based on the content management software (CMS) you are using.</p>
	<div class="dl-lts-options">
		<?php foreach ($content['download_urls'] as $key => $values) { ?>
			<p><strong><a class="dl-link" href="<?php echo $values['url'];?>&rtype=lts"><?php echo 'Compatible with ' . $values['title']; ?></a></strong></p>
		<?php } ?>
	</div>
</div>