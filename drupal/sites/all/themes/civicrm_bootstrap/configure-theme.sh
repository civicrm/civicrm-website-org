# drush en -y civicrm_bootstrap
# drush vset theme_default civicrm_bootstrap
# drush vset cache 0
# drush vset preprocess_css 0
# drush vset preprocess_js 0
drush sql-query "UPDATE block SET region = -1 WHERE theme='civicrm_bootstrap';"
echo "Ensure that we are not overwritting the bootstraps breadrumb implementation and that 'Show current page title at end' is not ticked."
# drush cc all
