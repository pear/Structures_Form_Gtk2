<?php
/**
 * A Structures_Form element for a submit button.
 *
 * This class implement Structures_Form_ElementInterface.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'Structures/Form/Element/Gtk2/Button.php';
class Structures_Form_Element_Gtk2_Submit extends Structures_Form_Element_Gtk2_Button {

    /**
     * Constructor. Calls parent constructor with a callback.
     *
     * @access public
     * @param  object $form  The Structures_Form.
     * @param  string $label The string to be used as the label.
     * @param  string $text  The default text (optional).
     * @param  array  $size  The height and width of the entry (optional).
     * @param  object $style The GtkStyle to apply to the entry (optional).
     * @return void
     */
    public function __construct(Structures_Form $form, $label = '',
                                $text = Gtk::STOCK_OK, $size = null,
                                $style = null)
    {
        // Call the parent constructor.
        parent::__construct($form, $label, $text, array($form, 'submit'),
                            $size, $style
                            );
    }
}
?>