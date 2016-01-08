<div <?php print 'class="civi-banner-image civi-banner-image-'.$row->nid.'"'?> >





<?php if (!empty($title) || !empty($description)): ?>
  <div class="container">
    <?php if (!empty($title)): ?>
      <h1><?php print $title ?></h1>
    <?php endif ?>

    <?php if (!empty($description)): ?>
      <?php print $description ?>
    <?php endif ?>
  </div>
<?php endif ?>
</div>
