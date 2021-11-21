<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_edamame
 *
 * @copyright   Copyright (C) 2021 Alison Keen
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Edamame Component
 *
 * @since  0.0.1
 */
class EdamameViewRationale extends JViewLegacy
{
  /**
   * Display the 'rationale' view - 
   * which shows the details of why each grade on the 
   * checklist was given (e.g. policy statements on the topic, 
   * previous votes in parliament) 
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  function display($tpl = null)
  {
    // Assign data to the view
    $this->msg = $this->get('Msg');

    // Check for errors.
    $errors = $this->get('Errors');

    if (null !== $errors)
    {
      if (count($errors = $this->get('Errors'))) {
        JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

        return false;
      }
    }

    // Display the view
    parent::display($tpl);
  }
}

