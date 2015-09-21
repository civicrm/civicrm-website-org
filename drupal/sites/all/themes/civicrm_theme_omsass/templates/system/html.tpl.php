<?php

/**
 * @file
 * Default theme implementation to display the basic html structure of a single
 * Drupal page.
 *
 * Variables:
 * - $css: An array of CSS files for the current page.
 * - $language: (object) The language the site is being displayed in.
 *   $language->language contains its textual representation.
 *   $language->dir contains the language direction. It will either be 'ltr' or
 *   'rtl'.
 * - $rdf_namespaces: All the RDF namespace prefixes used in the HTML document.
 * - $grddl_profile: A GRDDL profile allowing agents to extract the RDF data.
 * - $head_title: A modified version of the page title, for use in the TITLE
 *   tag.
 * - $head_title_array: (array) An associative array containing the string parts
 *   that were used to generate the $head_title variable, already prepared to be
 *   output as TITLE tag. The key/value pairs may contain one or more of the
 *   following, depending on conditions:
 *   - title: The title of the current page, if any.
 *   - name: The name of the site.
 *   - slogan: The slogan of the site, if any, and if there is no title.
 * - $head: Markup for the HEAD section (including meta tags, keyword tags, and
 *   so on).
 * - $styles: Style tags necessary to import all CSS files for the page.
 * - $scripts: Script tags necessary to load the JavaScript files and settings
 *   for the page.
 * - $page_top: Initial markup from any modules that have altered the
 *   page. This variable should always be output first, before all other dynamic
 *   content.
 * - $page: The rendered page content.
 * - $page_bottom: Final closing markup from any modules that have altered the
 *   page. This variable should always be output last, after all other dynamic
 *   content.
 * - $classes String of classes that can be used to style contextually through
 *   CSS.
 *
 * @see template_preprocess()
 * @see template_preprocess_html()
 * @see template_process()
 * @see omega_preprocess_html()
 */
?><!DOCTYPE html>
<?php if (omega_extension_enabled('compatibility') && omega_theme_get_setting('omega_conditional_classes_html', TRUE)): ?>
  <!--[if IEMobile 7]><html class="no-js ie iem7" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"><![endif]-->
  <!--[if lte IE 6]><html class="no-js ie lt-ie9 lt-ie8 lt-ie7" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"><![endif]-->
  <!--[if (IE 7)&(!IEMobile)]><html class="no-js ie lt-ie9 lt-ie8" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"><![endif]-->
  <!--[if IE 8]><html class="no-js ie lt-ie9" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"><![endif]-->
  <!--[if (gte IE 9)|(gt IEMobile 7)]><html class="no-js ie" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>><![endif]-->
  <!--[if !IE]><!--><html class="no-js" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>><!--<![endif]-->
<?php else: ?>
  <html class="no-js" lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>
<?php endif; ?>
<head>
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <?php print $styles; ?>
  <?php print $scripts; ?>
  <script src="//cdn.jsdelivr.net/jquery.scrollto/2.1.0/jquery.scrollTo.min.js"></script>

  <script>
    (function ($) {
  $(document).ready(function() {
    // Bind to the click of all links with a #hash in the href
    $('a[href^="/#"]').click(function(e) {
      // Prevent the jump and the #hash from appearing on the address bar
      e.preventDefault();
      // Scroll the window, stop any previous animation, stop on user manual scroll
      // Check https://github.com/flesler/jquery.scrollTo for more customizability
      console.log(this.hash);
      $(window).scrollTo(this.hash, {duration:1000, interrupt:true, offset:-96});
    });

    $('.l-header').scrollToFixed();
    $('.mmenu-nav').on('closed.mm', function() {
      var mmenu = $(this);
      if (mmenu.hasClass('mm-horizontal')) {
        // For horizontal mmenu when slidingSubmenus option is set to Yes.
        $('> .mm-panel', mmenu).addClass('mm-hidden').removeClass('mm-opened mm-subopened mm-highest mm-current');
        $('#mm-5').removeClass('mm-hidden mm-subopened').addClass('mm-opened mm-current');
      }
      else {
        // For vertical mmenu when slidingSubmenus option is set to No.
        $('> .mm-panel .mm-opened', mmenu).removeClass('mm-opened');
      }
    });
  
    function fix_body_height(){
    var h = $('#mmenu_right').outerHeight();
    if( h < $(window).height() ){
    h = $(window).height(); 
    }
    $('html.mm-opened, body').height(h);
    }

    <?php if (drupal_get_path_alias(current_path()) == 'nw-home'): ?>  
    jQuery(window).scroll(function() {
      if (jQuery('body').scrollTop() == 0)
        jQuery('.l-header.after').css({'position':'fixed','bottom':0});
      else if (!jQuery('.l-header').hasClass('scroll-to-fixed-fixed'))
        jQuery('.l-header.after').css({'position':'static'});
      bodyPos = jQuery('body').scrollTop() + jQuery('.l-header').outerHeight();

        if(bodyPos > jQuery('.l-region--blue').position().top && bodyPos < jQuery('.l-region--blue').outerHeight()+jQuery('.l-region--blue').position().top) {
          jQuery('.block--menu-menu-primary-menu-home-page a').removeClass('selected');
          jQuery('.block--menu-menu-primary-menu-home-page a[href="/#features"]').addClass('selected');
        } else jQuery('.block--menu-menu-primary-menu-home-page a[href="/#features"]').removeClass('selected');

        if(bodyPos > jQuery('#block-views-get-started-block').position().top && bodyPos < jQuery('#block-views-get-started-block').outerHeight()+jQuery('#block-views-get-started-block').position().top) {
          jQuery('.block--menu-menu-primary-menu-home-page a').removeClass('selected');
          jQuery('.block--menu-menu-primary-menu-home-page a[href="/#get-started"]').addClass('selected');
        } else jQuery('.block--menu-menu-primary-menu-home-page a[href="/#get-started"]').removeClass('selected');

        if(bodyPos > jQuery('#block-views-get-started-block-1').position().top && bodyPos < jQuery('#block-views-get-started-block-1').outerHeight()+jQuery('#block-views-get-started-block-1').position().top) {
          jQuery('.block--menu-menu-primary-menu-home-page a').removeClass('selected');
          jQuery('.block--menu-menu-primary-menu-home-page a[href="/#get-involved"]').addClass('selected');
        } else jQuery('.block--menu-menu-primary-menu-home-page a[href="/#get-involved"]').removeClass('selected');

        if(bodyPos > jQuery('#block-views-get-started-block-2').position().top && bodyPos < jQuery('#block-views-get-started-block-2').outerHeight()+jQuery('#block-views-get-started-block-2').position().top) {
          jQuery('.block--menu-menu-primary-menu-home-page a').removeClass('selected');
          jQuery('.block--menu-menu-primary-menu-home-page a[href="/#support-us"]').addClass('selected');
        } else jQuery('.block--menu-menu-primary-menu-home-page a[href="/#support-us"]').removeClass('selected');


    });
    <?php endif; ?>
  });
})(jQuery);

  </script>

  

</head>
<body<?php print $attributes;?>>
  <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>
