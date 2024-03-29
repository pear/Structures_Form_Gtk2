<?php
/**
 * A Structures_Form_Gtk2 Exception class. This class extends PEAR_Exception 
 * but does not add any additional functionality. It exists to make it easier
 * to catch Structures_Form_Exception specific exceptions.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'PEAR/Exception.php';
class Structures_Form_Gtk2_Exception extends PEAR_Exception {
    // Empty.
}
?>