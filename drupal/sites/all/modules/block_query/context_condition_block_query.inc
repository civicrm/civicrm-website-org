<?php
/**
 * @file
 * Expose queries as a context condition.
 * 
 * @author author name
 */

class context_condition_block_query extends context_condition {
  /**
   * Omit condition values. We will provide a custom input form for our conditions.
   * 
   * @return array
   *   Returns and empty array.
   */
  function condition_values() {
    return array();
  }

  /**
   * Gets the values of fetched context.
   * 
   * @param mixed $context
   *   The matched options of the form.
   * 
   * @return array
   *   Returns the array default value of the fetched context.
   */
  function condition_form($context) {
    $form = parent::condition_form($context);
    unset($form['#options']);

    $form['#type'] = 'textarea';
    $form['#default_value'] = implode("\n", $this->fetch_from_context($context, 'values'));
    return $form;
  }

  /**
   * Condition form submit handler.
   * 
   * @param mixed $values
   *   The values of the fields on the form.
   * 
   * @return array
   *   Returns an array of the trimmed items.
   */
  function condition_form_submit($values) {
    $parsed = array();
    $items = explode("\n", $values);
    if (!empty($items)) {
      foreach ($items as $v) {
        $v = trim($v);
        if (!empty($v)) {
          $parsed[$v] = $v;
        }
      }
    }
    return $parsed;
  }

  /**
   * Executes the submitted condition form.
   */
  function execute() {
    if ($this->condition_used()) {

      // Include both the path alias and normal path for matching.
      $current_queries = array();
      foreach ($_GET as $getkey => $value) {
        if (!is_array($getkey) and !is_array($value)) {
          $getkeylower = drupal_strtolower($getkey);
          $valuelower = drupal_strtolower($value);

          if ($getkeylower != 'q') {
            $current_queries[] = $getkeylower . '=' . $valuelower;
          }
        }
      }
      foreach ($this->get_contexts() as $context) {
        $queries = $this->fetch_from_context($context, 'values');
        if ($this->match($current_queries, $queries)) {
          $this->condition_met($context);
        }
      }
    }
  }

  /**
   * Match the subject against a set of regex patterns.
   * Similar to drupal_match_path() but also handles negation 
   * through the use of the ~ character.
   *
   * @param mixed $subjects
   *   The subject string or an array of strings to be matched.
   * @param array $patterns
   *   An array of patterns. Any patterns that begin with ~ are considered
   *   negative or excluded conditions.
   */
  protected function match($subjects, $patterns) {
    static $regexps;
    $match = FALSE;
    $positives = $negatives = 0;
    $subjects = !is_array($subjects) ? array($subjects) : $subjects;
    foreach ($patterns as $pattern) {
      if (strpos($pattern, '~') === 0) {
        $negate = TRUE;
        $negatives++;
      }
      else {
        $negate = FALSE;
        $positives++;
      }
      $pattern = ltrim($pattern, '~');
      if (!isset($regexps[$pattern])) {
        $regexps[$pattern] = '/^(' . preg_replace(array(
            '/(\r\n?|\n)/', '/\\\\\*/', '/(^|\|)\\\\<front\\\\>($|\|)/',
          ),
          array(
            '|', '.*', '\1' . preg_quote(variable_get('site_frontpage', 'node'), '/') . '\2',
          ),
          preg_quote($pattern, '/')
        ) . ')$/';
      }
      foreach ($subjects as $value) {
        if (preg_match($regexps[$pattern], $value)) {
          if ($negate) {
            return FALSE;
          }
          $match = TRUE;
        }
      }
    }
    // If there are **only** negative conditions and we've gotten this far none
    // we actually have a match.
    if ($positives === 0 && $negatives) {
      return TRUE;
    }
    return $match;
  }
}
