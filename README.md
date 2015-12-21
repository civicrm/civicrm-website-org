#Development workflow

All new code, etc. should be developed locally, commited to this repository, tested on www-test.civicrm.org, and the deployed on production. www-test should NOT be used for code based development.

It is good practice to use www-test to experiment with anything that does not happen at the code level (like configuration changes, creating views, permissions changes, etc.) but if you are making trivial changes, just adding content, etc., and you are confident of the results, please feel free to make the changes directly on the live site.

Note that many people have access to www-test, and it gets over written with data from www-prod on a regular basis, e.g. when testing upgrades of CiviCRM.  Therefore, you should let people on the infra list know about any changes you are making on www-test so they don't overwrite them, and NOT assume that your config changes will be on www-test next time you look.  Backup/Export any complex views, etc. to ensure they are there next time you look.

**Note:** Access www-test.civicrm.org with http username/password civicrm/civicrm.

## Development branches

www-prod should track the master branch.  Developments should happen on seperate branches and be merged to master when ready to deploy.

## Custom modules

All custom modules should be added to the sites/all/modules/custom directory and follow the naming convention civicrm_org_module_name.

## Releases

http://civicrm.org is served from www-prod.  All source-code is owned by the co user.  To do a release, use the latest code from the master branch to create a tag and then check out the tag on www-prod, e.g.

```bash
localhost$ ssh www-test
me@www-test$ sudo -u co -H bash
co@www-test$ cd /var/www/civicrm-website-org/
co@www-test$ git checkout master
co@www-test$ git pull
co@www-test$ ./tag.sh origin
## Make a mental note of the tag name (e.g. "deploy-2014-10-15-22-42")
localhost$ ssh www-prod
me@www-prod$ sudo -u co -H bash
co@www-prod$ cd /var/www/civicrm-website-org/
co@www-prod$ git fetch origin
## Checkout the appropriate tag, e.g.
co@www-prod$ git checkout deploy-2014-10-15-22-42
## Optional: If config/*.conf has changed, then restart nginx.
me@www-prod$ sudo service nginx restart
```

This process gives a clear trail of the timeline for code that has been deployed on www-prod -- which can assist in future debugging/auditing.

# Syncing to test and local environments

Syncing to www-test and local development environments is done in the standard way (mysqldump and restore the databases and rsync/copy the files).  You can then do a git pull (and so on) to check out appropriate code.

There is a script /home/michael/sync_co.sh on www-test that does this.  It needs to be run as michael at the moment, but we could generalising it should be trivial.

You should not need to worry about backing up the www-test database because no important data should be stored there (see development workflow above).

# Local development environments

You can develop locally as long as you are not storing any unencrypted personal data in your local development environment.

Drupal and CiviCRM databases can be encrypted on www-test.civicrm.org before being transferred to local development environments.

The site is hosted with nginx and this repository also contains some nginx rewrites that you can include with something like the below:


```
server {

    # Standard Drupal and CiviCRM nginx configuration here

    include /var/www/civicrm-website-org/config/rewrites.conf; # <-- add this line

}
```

# Upgrades

Upgrades (especially CiviCRM upgrades) should be

1) tested on a local copy first, then commited to the repo, then
2) tested on www-test

If all went smoothly in both instances, they can be carried out on the production server.

Notes:
* The drush command 'drush updb' is useful for just applying db upgrades when the code upgrade has already been done with a git pull (as is the case on www-test and www-prod).
* The drush command 'civicrm-upgrade-db' is useful for upgrading CiviCRM from the command line

Needless to say, if you do notice anything going wrong
and on the test infrastructure before being carried out on the production server.

Put the site into maintanence mode before upgrading

# CiviCRM customisations

Any CiviCRM customisations should be places in the php and templates directory rather than being directly overwritted in order to make it easy to keep track of customisations through upgrades.

# Support

Please email michael@civicrm.org or totten@civicrm.org if you need any support with any of the above.
