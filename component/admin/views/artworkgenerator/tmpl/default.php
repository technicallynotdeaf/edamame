<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_edamame
 *
 * @copyright   (C) 2021 - Alison Keen 
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 *
 * THIS CODE IS TOTALLY BROKEN and needs rewriting, 
 * i obviously started it and just haven't come back to it yet
 * 
 * Quick overview to jog my memory:
 * 1, read the likely variables from last time we submitted the form
 * 2, check for values we need to feed back to database, put into DB
 * 3, then read new status from database and display
 */



// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$jinput = JFactory::getApplication()->input;

// need to define some objects so they have whole-page scope and can be accessed;
$related_bill = null;
$a_quote = null;

## BROKEN needs to use the HElper::getDB function... 
$db = JDatabaseDriver::getInstance( $option );



// ========== End of under-the-hood processing, start of displaying page ==========


echo "<h2>Artwork Generator Page</h2>";


?>
