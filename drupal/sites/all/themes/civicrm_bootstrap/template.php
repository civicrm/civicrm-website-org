<?php

function civicrm_bootstrap_menu_tree__primary(&$variables)
{
    return '<ul class="menu nav navbar-nav navbar-right">'.$variables['tree'].'</ul>';
}

function civicrm_bootstrap_menu_tree__secondary(&$variables)
{
    return '<ul class="menu nav navbar-nav navbar-right navbar-secondary">'.$variables['tree'].'</ul>';
}

function civicrm_bootstrap_menu_link__menu_block__menu_primary_menu($variables)
{
    $element = $variables['element'];
    $sub_menu = '';

    if ($element['#below']) {
        $sub_menu = drupal_render($element['#below']);
    }

    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    if ($element['#original_link']['depth'] == 1) {
        $element['#attributes']['class'][] = 'col-sm-6 col-md-4 col-lg-2';
    }

    return '<li'.drupal_attributes($element['#attributes']).'>'.$output.$sub_menu."</li>\n";
}

function civicrm_bootstrap_menu_tree__menu_block__menu_primary_menu($variables)
{
    return '<ul class="menu">'.$variables['tree'].'</ul>';
}

function civicrm_bootstrap_preprocess_html(&$variables)
{
    // drupal_add_css('sites/default/files/wackymcdoo.css');

  if (page_manager_get_current_page()) {
      $variables['classes_array'][] = 'panel-page';
  }

    if (count(crumbs_get_trail()) > 1) {
        $variables['classes_array'][] = 'breadcrumbs';
    }
}

function civicrm_bootstrap_preprocess_page(&$variables)
{
    if (page_manager_get_current_page()) {
        $variables['panel_page'] = true;
    }
}

function civicrm_bootstrap_preprocess_region(&$variables)
{
    switch ($variables['region']) {
  // @todo is this actually used properly?
  case 'footer':
  $variables['classes_array'][] = 'row';

  break;
  }
}
