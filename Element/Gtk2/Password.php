<?php
/**
 * A Structures_Form element for one line of plain text disguised with ***
 *
 * The only difference between this class and Structures_Form_Element_GtkText is
 * that this class masks the displayed value using a single character ('*' by
 * default).
 *
 * The mask character can be changed by calling set_invisible_char() which is a
 * GtkEntry method.
 *
 * This class implement Structures_Form_ElementInterface.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'Structures/Form/Element/Gtk2Text.php';
class Structures_Form_Element_Gtk2_Password extends Structures_Form_Element_Gtk2Text {

    /**
     * Constructor.
     *
     * @access public
     * @param  object $form  The Structures_Form.
     * @param  string $label The string to be used as the label.
     * @param  string $text  The default text (optional).
     * @param  array  $size  The height and width of the entry (optional).
     * @param  object $style The GtkStyle to apply to the entry (optional).
     * @return void
     */
    public function __construct(Structures_Form $form, $label, $text = null,
                                $size = null, $style = null)
    {
        // Call the parent constructor.
        parent::__construct($form, $label, $text, $size, $style);

        // Make the text 'invisible'
        $this->set_visibility(false);
    }
}
?>