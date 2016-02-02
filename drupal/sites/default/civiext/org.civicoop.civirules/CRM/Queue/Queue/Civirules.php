<?php

class CRM_Queue_Queue_Civirules extends CRM_Queue_Queue_Sql {

  /**
   * Determine number of items remaining in the queue
   *
   * @return int
   */
  function numberOfItems() {
    return CRM_Core_DAO::singleValueQuery("
      SELECT count(*)
      FROM civicrm_queue_item
      WHERE queue_name = %1
      and (release_time is null OR release_time <= NOW())
    ", array(
      1 => array($this->getName(), 'String'),
    ));
  }

  /**
   * Get the next item
   *
   * @param $lease_time seconds
   *
   * @return object with key 'data' that matches the inputted data
   */
  function claimItem($lease_time = 3600) {
    $sql = "
      SELECT id, queue_name, submit_time, release_time, data
      FROM civicrm_queue_item
      WHERE queue_name = %1
      and (release_time is null OR release_time <= NOW())
      ORDER BY weight ASC, release_time ASC, id ASC
      LIMIT 1
    ";
    $params = array(
      1 => array($this->getName(), 'String'),
    );
    $dao = CRM_Core_DAO::executeQuery($sql, $params, TRUE, 'CRM_Queue_DAO_QueueItem');
    if (is_a($dao, 'DB_Error')) {
      // FIXME - Adding code to allow tests to pass
      CRM_Core_Error::fatal();
    }

    if ($dao->fetch()) {
      $nowEpoch = CRM_Utils_Time::getTimeRaw();
      if ($dao->release_time === NULL || strtotime($dao->release_time) < $nowEpoch) {
        CRM_Core_DAO::executeQuery("UPDATE civicrm_queue_item SET release_time = %1 WHERE id = %2", array(
          '1' => array(date('YmdHis', $nowEpoch + $lease_time), 'String'),
          '2' => array($dao->id, 'Integer'),
        ));
        // work-around: inconsistent date-formatting causes unintentional breakage
        #        $dao->submit_time = date('YmdHis', strtotime($dao->submit_time));
        #        $dao->release_time = date('YmdHis', $nowEpoch + $lease_time);
        #        $dao->save();
        $dao->data = unserialize($dao->data);
        return $dao;
      }
      else {
        CRM_Core_Error::debug_var('not ready for release', $dao);
        return FALSE;
      }
    }
    else {
      CRM_Core_Error::debug_var('no items found');
      return FALSE;
    }
  }

  /**
   * Get the next item, even if there's an active lease
   *
   * @param $lease_time seconds
   *
   * @return object with key 'data' that matches the inputted data
   */
  function stealItem($lease_time = 3600) {
    $sql = "
      SELECT id, queue_name, submit_time, release_time, data
      FROM civicrm_queue_item
      WHERE queue_name = %1
      ORDER BY weight ASC, release_time ASC, id ASC
      LIMIT 1
    ";
    $params = array(
      1 => array($this->getName(), 'String'),
    );
    $dao = CRM_Core_DAO::executeQuery($sql, $params, TRUE, 'CRM_Queue_DAO_QueueItem');
    if ($dao->fetch()) {
      $nowEpoch = CRM_Utils_Time::getTimeRaw();
      CRM_Core_DAO::executeQuery("UPDATE civicrm_queue_item SET release_time = %1 WHERE id = %2", array(
        '1' => array(date('YmdHis', $nowEpoch + $lease_time), 'String'),
        '2' => array($dao->id, 'Integer'),
      ));
      $dao->data = unserialize($dao->data);
      return $dao;
    }
    else {
      CRM_Core_Error::debug_var('no items found');
      return FALSE;
    }
  }

}