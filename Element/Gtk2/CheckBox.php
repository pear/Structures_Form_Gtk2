<?php
/**
 * A Structures_Form element for a checkbox.
 *
 * This class implement Structures_Form_ElementInterface.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'Structures/Form/ElementInterface.php';
class Structures_Form_Element_Gtk2_CheckBox extends GtkCheckButton implements Structures_Form_ElementInterface {

    /**
     * The Structures_Form object.
     *
     * @access public
     * @var    object
     */
    public $form;

    /**
     * The value of the element when not checked.
     * 
     * @access public
     * @var    mixed
     */
    public $off = null;
    
    /**
     * Constructor.
     *
     * @access public
     * @param  object  $form    The Structures_Form.
     * @param  string  $label   The string to be used as the label.
     * @param  mixed   $value   The value if the checkbox is checked.
     * @param  mixed   $off     The value if the checkbox is not checked.
     * @param  boolean $checked Whether or not the checkbox is checked by
     *                          default.
     * @param  array   $size    The height and width of the entry (optional).
     * @param  object  $style   The GtkStyle to apply to the entry (optional).
     * @return void
     */
    public function __construct(Structures_Form $form, $label, $value,
                                $off = null, $checked = false, $size = null,
                                $style = null)
    {
        // Call the parent constructor.
        parent::__construct();

        // Set the form object.
        $this->form = $form;

        // Set the label.
        $this->setLabel($label);
        
        // Set the value.
        $this->setValue($value);

        // Set the off value.
        $this->off = $off;

        // Mark the checkbox as checked or not.
        $this->set_active($checked);

        // Set the size if it was given.
        if (is_array($size)) {
            $this->set_size_request($size[0], $size[1]);
        }

        // Set the style if it was given.
        if ($style instanceof GtkStyle) {
            $this->set_style($style);
        }
    }

    /**
     * Sets an element's value.
     *
     * This method should set the value of the widget not just set some data
     * that is retrieved later. If the widget is a GtkEntry, this method should
     * call set_text(). If the widget is a GtkComboBox, this method should set
     * the active row.
     *
     * @access public
     * @param  string  $value The text to put in the entry.
     * @return boolean true if the value was changed.
     */
    public function setValue($value)
    {
        $this->set_data('value', $value);
        return true;
    }

    /**
     * Returns element's value.
     *
     * This method should return the widget's value not just some data from the
     * widget (i.e. set with set_data()). For example if the widget is a 
     * GtkEntry, this method should call get_text(). If the widget is a
     * GtkComboBox, this method should return the value of the column
     * identified when the element was constructed for the given row.
     *
     * If the box is not checked, NULL will be returned.
     *
     * @access public
     * @return string
     */
    public function getValue()
    {
        if ($this->get_active()) {
            return $this->get_data('value');
        } else {
            return $this->off;
        }
    }

    /**
     * Clears the current value of the element.
     *
     * This method should clear the current value if possible. For example, if
     * the widget is a GtkEntry, this method should pass null to set_text(). If
     * the value could not be cleared for some reason (the item is frozen or it
     * is not possible to clear the value (selection type = browse)) this
     * method should return false.
     *
     * @access public
     * @return boolean true if the value was cleared.
     */
    public function clearValue()
    {
        return $this->setValue(null);
    }

    /**
     * Returns the element type.
     * 
     * This method must return a string identifying the element type, such as 
     * text, password, submit, etc.
     *
     * @access public
     * @return string The element type.
     */
    public function getType()
    {
        return 'checkbox';
    }

    /**
     * Sets the element name.
     *
     * This method exists to maintain consistency in the interface. It should
     * simply call set_name which is a GtkWidget method and should be avialable
     * to all elements.
     *
     * @access public
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->set_name($name);
    }

    /**
     * Returns the element's name.
     *
     * This method exists to maintain consistency in the interface. It should
     * simply call get_name which is a GtkWidget method and should be available
     * to all elements.
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->get_name();
    }

    /**
     * Freezes the element so that its value may not be changed.
     *
     * Again this method exists only to maintain consistency in the interface.
     * It should just pass false to set_sensitive().
     *
     * To make life easier down the road this method should also call
     * set_data('frozen', true);
     *
     * @access public
     * @return void
     */
    public function freeze()
    {
        // Make the widget insensitive.
        $this->set_sensitive(false);

        // Set a data value for ease of programming later.
        $this->set_data('frozen', true);
    }

    /**
     * Unfreezes the element so that its value may be changed.
     *
     * Again this method exists only to maintain consistency in the interface.
     * It should just pass true to set_sensitive().
     *
     * To make life easier down the road this method should also call
     * set_data('frozen', false);
     *
     * @access public
     * @return void
     */
    public function unfreeze()
    {
        // Make the widget sensitive.
        $this->set_sensitive(true);
                                                
        // Set a data value for ease of programming later.
        $this->set_data('frozen', false);
    }

    /**
     * Returns whether or not the element is currently frozen.
     * 
     * This method should just return the value from get_data('frozen')
     * @access public
     * @return boolean
     */
    public function isFrozen()
    {
        return (bool) $this->get_data('frozen');
    }

    /**
     * Sets the GtkLabel that identifies the element.
     *
     * @access public
     * @param  string $label
     * @return void
     */
    public function setLabel($label)
    {
        $this->set_label($label);
    }

    /**
     * Returns the GtkLabel that identifies the element.
     *
     * Because of the way checkboxes are set up, this method returns ' '. If 
     * it doesn't the checkbox will have two labels, one to the left and one to
     * the right.
     *
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return ' ';
    }

    /**
     * Adds an event handler for the element.
     *
     * @access public
     * @param  string  $eventName The name of the event.
     * @param  mixed   $callback  The callback to call when the event occurs.
     * @return integer An identifier for the callback.
     */
    public function addEventHandler($eventName, $callback)
    {
        return $this->connect($eventName, $callback);
    }
}
?>