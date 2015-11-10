<?php



 /**
  * @file
  * template.php
  */
 function civicrm_bootstrap_menu_tree__primary(&$variables)
 {
     return '<ul class="menu nav navbar-nav navbar-right">'.$variables['tree'].'</ul>';
 }

function civicrm_bootstrap_menu_link__menu_block__menu_primary_menu($variables)
{
    $element = $variables['element'];
    $sub_menu = '';

    if ($element['#below']) {
      $sub_menu = drupal_render($element['#below']);
    }
    

    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    if($element['#original_link']['depth']==1){
        $element['#attributes']['class'][]='col-sm-2';
    }

    return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

function civicrm_bootstrap_menu_tree__menu_block__menu_primary_menu($variables)
{
    return '<ul class="menu">' . $variables['tree'] . '</ul>';
}
