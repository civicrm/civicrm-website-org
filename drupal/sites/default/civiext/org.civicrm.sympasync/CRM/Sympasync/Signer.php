<?php

/**
 * Class CRM_Sympasync_Signer
 *
 * This class is able to sign and validate any unsubscribe messages.
 */
class CRM_Sympasync_Signer {

  /**
   * Time to Live for signed tokens (seconds).
   *
   * 1 day: 24 * 60 * 60 = 86400
   */
  const TTL = 86400;

  /**
   * @param int $gid
   * @param string $email
   */
  public static function createConfirmationUrl($gid, $email) {
    $signer = new CRM_Utils_Signer(
      CRM_Core_BAO_Setting::getItem('Sympa', 'sympaPrivateToken'),
      array('gid', 'email', 'ts')
    );

    $params = array(
      'gid' => $gid,
      'email' => $email,
      'ts' => CRM_Utils_Time::getTimeRaw(),
    );
    $params['sig'] = $signer->sign($params);

    return CRM_Utils_System::url('civicrm/sympa/unsubscribe/confirm', $params, TRUE, NULL, FALSE);
  }

  /**
   * @param array $params
   *   The GET parameters -- i.e. 'gid', 'email', 'ts', 'sig'.
   * @return array|FALSE
   *   The validated gid+email. Otherwise, FALSE.
   */
  public static function validateConfirmation($params) {
    $signer = new CRM_Utils_Signer(
      CRM_Core_BAO_Setting::getItem('Sympa', 'sympaPrivateToken'),
      array('gid', 'email', 'ts')
    );
    if (!$signer->validate($params['sig'], $params)) {
      return FALSE;
    }
    if (!is_numeric($params['ts']) || $params['ts'] + self::TTL < CRM_Utils_Time::getTimeRaw()) {
      return FALSE;
    }
    return array(
      'gid' => $params['gid'],
      'email' => $params['email'],
    );
  }

}
