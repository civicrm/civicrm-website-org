<?php

/**
 * Class CRM_Sympasync_Mail
 *
 * This class manages any email composition required by sympasync.
 */
class CRM_Sympasync_Mail {

  public static function sendUnsubscribeConfirmation($gid, $email) {
    $group = CRM_Contact_DAO_Group::findById($gid);
    $preferredFormat = self::getPreferredFormat($email);
    list($domainEmailName, $domainEmailAddress) = CRM_Core_BAO_Domain::getNameAndEmail();
    $emailDomain = CRM_Core_BAO_MailSettings::defaultDomain();
    $confirmationUrl = CRM_Sympasync_Signer::createConfirmationUrl($gid, $email);

    $headers = array(
      'Subject' => ts('Unsubscribe Request: %1', array(1 => $group->title)),
      'From' => "\"$domainEmailName\" <do-not-reply@$emailDomain>",
      'To' => $email,
      'Reply-To' => "do-not-reply@$emailDomain",
      'Return-Path' => "do-not-reply@$emailDomain",
    );

    require_once 'Mail/mime.php';
    $message = new Mail_mime("\n");
    if ($preferredFormat == 'HTML' || $preferredFormat == 'Both') {
      $message->setHTMLBody(self::getConfirmationHtml($group, $email, $confirmationUrl));
    }
    if ($preferredFormat == 'Text' || $preferredFormat == 'Both') {
      $message->setTxtBody(self::getConfirmationText($group, $email, $confirmationUrl));
    }

    CRM_Core_Config::getMailer()
      ->send($email, $message->headers($headers), CRM_Utils_Mail::setMimeParams($message));
  }

  private static function getConfirmationHtml($group, $email, $url) {
    return sprintf('<html><body><p>%s</p><p><a href="%s">%s</a></p><p>%s</p></body></html>',
      ts('A request was made to remove "%1" from the mailing list "%2". To confirm this request, please open:', array(
        1 => htmlentities($email),
        2 => htmlentities($group->title),
      )),
      htmlentities($url), htmlentities($url),
      ts('If this was sent in error, you may ignore this message.')
    );
  }

  private static function getConfirmationText($group, $email, $url) {
    return implode("\n\n", array(
      ts('A request was made to remove "%1" from the mailing list "%2". To confirm this request, please open:', array(
        1 => $email,
        2 => $group->title,
      )),
      $url,
      ts('If this was sent in error, you may ignore this message.'),
    ));
  }

  /**
   * @param string $email
   * @return string
   *   Text,HTML,Both
   */
  private static function getPreferredFormat($email) {
    $value = CRM_Core_DAO::singleValueQuery(
      '
        SELECT c.preferred_mail_format
        FROM civicrm_contact c
        INNER JOIN civicrm_email e ON e.contact_id = c.id
        WHERE e.email = %1
        LIMIT 1
      ', array(
        1 => array($email, 'String'),
      )
    );
    if (empty($preferredFormat)) {
      return 'Both';
    }
    return $value;
  }

}
