<?php

/**
 * This class handles period conditions
 * It provides a list with options for period
 * and it converts it to a start and end date of the period
 *
 */
class CRM_CivirulesConditions_Utils_Period {

  /**
   * Returns the possible options
   *
   * @return array
   */
  public static function Options() {
    $definitions = self::PeriodDefinitions();
    $options = array();
    foreach($definitions as $key => $definition) {
      $options[$key] = $definition['label'];
    }
    return $options;
  }

  /**
   * Returns all possible period options and their definition
   *
   * A definition consist of a label of the period and eventually extra replacements
   * e.g. In the period Last nnn days, the 'nnn' should be given by the user and should by
   * taken into account by calculating the start and end date
   *
   * @return array
   */
  private static function PeriodDefinitions() {
    return array(
      'this month' => array(
        'label' => ts('This month'),
        'replacements' => array()
      ),
      'previous month' => array(
        'label' => ts('Previous month'),
        'replacements' => array()
      ),
      'last 30 days' => array(
        'label' => ts('Last 30 days'),
        'replacements' => array()
      ),
      'last 12 months' => array(
        'label' => ts('Last 12 months'),
        'replacements' => array()
      ),
      'last 13 months' => array(
        'label' => ts('Last 13 months'),
        'replacements' => array()
      ),
      'this year' => array(
        'label' => ts('This year'),
        'replacements' => array()
      ),
      'previous year' => array(
        'label' => ts('Previous year'),
        'replacements' => array()
      ),
      'last nnn days' => array(
        'label' => ts('Last nnn days'),
        'replacements' => array(
          'nnn' => ts('days'),
        )
      ),
      'last nnn weeks' => array(
        'label' => ts('Last nnn weeks'),
        'replacements' => array(
          'nnn' => ts('weeks'),
        )
      ),
      'last nnn months' => array(
        'label' => ts('Last nnn months'),
        'replacements' => array(
          'nnn' => ts('months'),
        )
      ),
      'last nnn years' => array(
        'label' => ts('Last nnn years'),
        'replacements' => array(
          'nnn' => ts('years'),
        )
      ),
    );
  }

  /**
   * Returns an array with all possible replacements
   *
   * The array looks like
   *  xxx => array(
   *    'last xxx years',
   *    'last xxx months',
   *    ...
   *  )
   *
   * @return array
   */
  private static function getReplacementOptions() {
    $definitions = self::PeriodDefinitions();
    $replacements = array();
    foreach($definitions as $key => $definition) {
      foreach($definition['replacements'] as $replacement => $suffix) {
        if (!isset($replacements[$replacement])) {
          $replacements[$replacement] = array();
        }
        $replacements[$replacement][] = $key;
      }
    }
    return $replacements;
  }

  /**
   * Returns an array with all possible replacements
   *
   * The array looks like
   *  last xxx years => array(
   *    'name' => xxx,
   *    'suffix' => Days
   *    ...
   *  )
   *
   * @return array
   */
  private static function getReplacementOptionsByPeriod() {
    $definitions = self::PeriodDefinitions();
    $replacements = array();
    foreach($definitions as $key => $definition) {
      foreach($definition['replacements'] as $replacement => $suffix) {
        $replacements[$key][] = array(
          'name' => $replacement,
          'suffix' => $suffix,
        );
      }
    }
    return $replacements;
  }

  /**
   * Add fields to the form for period selection
   *
   * @param $form
   */
  public static function buildQuickForm(&$form) {
    $form->add('select', 'period', ts('Period'), array('' => ts('All time')) + CRM_CivirulesConditions_Utils_Period::Options());

    $replacements = self::getReplacementOptions();
    foreach($replacements as $replacement_key => $replacement_periods) {
      $form->add('text', $replacement_key, ts($replacement_key));
    }
    $form->assign('period_replacements', $replacements);
    $form->assign('period_replacements_by_period', json_encode(self::getReplacementOptionsByPeriod()));
  }

  public static function addRules(&$form) {
    $form->addFormRule(array('CRM_CivirulesConditions_Utils_Period', 'validatePeriod'));
  }

  public static function validatePeriod($fields) {
    $definitions = self::PeriodDefinitions();

    $period = $fields['period'];
    if (!empty($period) && isset($definitions[$period])) {
      $errors = array();
      foreach($definitions[$period]['replacements'] as $key => $label) {
         if (empty($fields[$key]) || !is_numeric($fields[$key])) {
           $errors[$key] = ts('You should enter a valid amount');
         }
       }
      if (count($errors)) {
        return $errors;
      }
    }
    return true;
  }

  /**
   * Set default values for a form based on the condition params
   *
   * @param array $defaultValues
   * @param array $condition_params
   * @return array
   */
  public static function setDefaultValues($defaultValues, $condition_params) {
    if (!empty($condition_params['period'])) {
      $defaultValues['period'] = $condition_params['period'];
    }
    if (isset($condition_params['replaceParameters']) && is_array($condition_params['replaceParameters'])) {
      foreach($condition_params['replaceParameters'] as $key => $val) {
        $defaultValues[$key] = $val;
      }
    }

    return $defaultValues;
  }

  /**
   * Parse submitted values from a form to the condition params
   *
   * @param array $submitValues
   * @param array $condition_params
   * @return array
   */
  public static function getConditionParams($submitValues, $condition_params) {
    $periods = self::PeriodDefinitions();
    $period = $condition_params['period'] = $submitValues['period'];
    foreach($periods[$period]['replacements'] as $key => $label) {
      $condition_params['replaceParameters'][$key] = $submitValues[$key];
    }
    return $condition_params;
  }

  private static function replaceParameters($period, $key, $replacement_params) {
    if (stripos($period, $key) === false) {
      throw new Exception($key .' is not set for period '.$period);
    }
    if (empty($replacement_params[$key])) {
      throw new Exception($key.' is not given for period '.$period);
    }
    return $replacement_params[$key];
  }

  public static function userFriendlyConditionParams($condition_params) {
    $periods = self::PeriodDefinitions();
    $p = $condition_params['period'];
    if (isset($periods[$p])) {
      $period = $periods[$p]['label'];
      foreach($periods[$p]['replacements'] as $key => $label) {
        try {
          $val = self::replaceParameters($period, $key, $condition_params['replaceParameters']);
          $period = str_replace($key, $val, $period);
        } catch (Exception $e) {
          //do nothing
        }
      }
    } else {
      $period = ts('all time');
    }
    return $period;
  }

  /**
   * Returns the start date of the selected period
   *
   * @param $period
   * @param array $replaceParameters
   * @return bool|\DateTime
   */
  public static function convertPeriodToStartDate($condition_params) {
    $period = $condition_params['period'];
    $replaceParameters = isset($condition_params['replaceParameters']) ? $condition_params['replaceParameters'] : array();
    $date = new DateTime();
    switch ($period) {
      case 'this month':
        $date->modify('first day of this month');
        return $date;
        break;
      case 'previous month':
        $date->modify('first day of previous month');
        return $date;
        break;
      case 'last 30 days':
        $date->modify('-30 days');
        return $date;
        break;
      case 'last nnn days':
        $xxx = self::replaceParameters($period, 'nnn', $replaceParameters);
        $date->modify('-'.$xxx.' days');
        return $date;
        break;
      case 'last 12 months':
        $date->modify('-12 months');
        return $date;
        break;
      case 'last 13 months':
        $date->modify('-13 months');
        return $date;
        break;
      case 'last nnn months':
        $xxx = self::replaceParameters($period, 'nnn', $replaceParameters);
        $date->modify('-'.$xxx.' months');
        return $date;
        break;
      case 'last nnn weeks':
        $xxx = self::replaceParameters($period, 'nnn', $replaceParameters);
        $date->modify('-'.$xxx.' weeks');
        return $date;
        break;
      case 'last nnn years':
        $xxx = self::replaceParameters($period, 'nnn', $replaceParameters);
        $date->modify('-'.$xxx.' years');
        return $date;
        break;
      case 'this year':
        $date->modify('first day of January this year');
        return $date;
        break;
      case 'previous year':
        $date->modify('first day of January previous year');
        return $date;
        break;
    }

    return false;
  }

  /**
   * Returns the end date of the selected period
   *
   * @param $period
   * @param array $replaceParameters
   * @return bool|\DateTime
   */
  public static function convertPeriodToEndDate($condition_params) {
    $period = $condition_params['period'];
    $replaceParameters = isset($condition_params['replaceParameters']) ? $condition_params['replaceParameters'] : array();
    $date = new DateTime();
    switch ($period) {
      case 'this month':
        $date->modify('last day of this month');
        return $date;
        break;
      case 'previous month':
        $date->modify('last day of previous month');
        return $date;
        break;
      case 'last 30 days':
        return $date;
        break;
      case 'last nnn days':
        return $date;
        break;
      case 'last 12 months':
        return $date;
        break;
      case 'last 13 months':
        return $date;
        break;
      case 'last nnn months':
        return $date;
        break;
      case 'last nnn weeks':
        return $date;
        break;
      case 'last nnn years':
        return $date;
        break;
      case 'this year':
        $date->modify('last day of December this year');
        return $date;
        break;
      case 'previous year':
        $date->modify('last day of December previous year');
        return $date;
        break;
    }

    return false;
  }

}
