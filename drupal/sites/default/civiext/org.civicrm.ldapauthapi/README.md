CiviCRM LDAP API Auth
=====================

This extension provides the duct tape to run LDAP authentication on Drupal
using LdapJS (http://ldapjs.org/). This extension does not provide any new
feature to CiviCRM, it merely uses the CiviCRM API because we are CiviCRM
developers so it was technically easier to implement.

Some services, such as the CiviCRM wiki, rely on this pseudo-LDAP
authentification in order to centralize account creation on CiviCRM.org.
However, CiviCRM.org is not running on top of LDAP, and a source which
prefers to remain anonymous was not too keen on setting up a new OpenLDAP
server for this purpose (which would have been the clean, but complicated
way to do it), so we duct-taped it with LdapJS.

Once enabled, this extension provides two APIs that can be queried by LdapJS:

* Contact.Authenticate (password authentication for a bind)
* Contact.Getfromuf (search)

This extension is based on the excellent ldapcivi+qlookup extensions by
Xavier Dutoit:

* https://github.com/TechToThePeople/ldapcivi
* https://civicrm.org/extensions/quick-contact-autocomplete

License
-------

(C) 2016 CiviCRM LLC
(C) 2014-2016 Xavier Dutoit

GNU AGPLv3. See LICENSE.txt.
