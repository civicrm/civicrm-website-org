<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2015
 * $Id$
 *
 */

/*
 * Settings metadata file
 */
return array(
  'sympaSqlUser' => array(
    'group_name' => 'Sympa',
    'group' => 'sympa',
    'name' => 'sympaSqlUser',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 64,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Sympa Remote MySQL User',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The credentials used by Sympa to get subscribers from the CiviCRM DB',
    'help_text' => NULL,
  ),
  'sympaSqlPass' => array(
    'group_name' => 'Sympa',
    'group' => 'sympa',
    'name' => 'sympaSqlPass',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 64,
      'maxlength' => 64,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Sympa Remote MySQL Password',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'The credentials used by Sympa to get subscribers from the CiviCRM DB',
    'help_text' => NULL,
  ),
  'sympaPrivateToken' => array(
    'group_name' => 'Sympa',
    'group' => 'sympa',
    'name' => 'sympaPrivateToken',
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_attributes' => array(
      'size' => 33,
      'maxlength' => 64,
    ),
    'html_type' => 'Text',
    'default' => NULL,
    'add' => '4.3',
    'title' => 'Sympa Private Token',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'This token is used to sign messages sent by CiviCRM-Sympa.',
    'help_text' => NULL,
  ),
);
