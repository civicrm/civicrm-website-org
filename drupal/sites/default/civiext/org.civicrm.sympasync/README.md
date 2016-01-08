# CiviCRM-Sympa Syncronization (org.civicrm.sympasync)

[CiviCRM](https://civicrm.org/) is a CRM suite for non-profit organizations.
[Sympa](http://www.sympa.org/) is a mailing list application which allows
subscribers to exchange messages.  With the `org.civicrm.sympasync` extension,
you can manage subscribers in CiviCRM while using Sympa's mailing list.

**How It Works**: Sympa supports [dynamic mailing
lists](http://www.sympa.org/manual/managing-members) using a SQL database.
This extension provides instructions and helpers for configuring a dynamic
mailing-list based on CiviCRM's SQL database. 

**Get Started**: Install the extension in CiviCRM and navigate to
"Administer: Communications: Sympa Sync".

**Security**: The Sympa application has a restricted view of the CiviCRM
database. It can read a list of active, primary email addresses for each
group in CiviCRM, but it cannot directly modify data or access other SQL
tables.

**Limitations**: Currently, this only supports simple, static groups.
If the concept works well with static groups, we can extend to smart,
dynamic groups.
