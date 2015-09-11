<header class="l-header" role="banner">
    <div class="l-header-inner">
      <div class="l-branding">
        <?php if ($logo): ?>
          <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="site-logo"><img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" /></a>
        <?php endif; ?>

        <?php print render($page['branding']); ?>
      </div>

      <?php print render($page['menu']); ?>
    </div>
  </header>