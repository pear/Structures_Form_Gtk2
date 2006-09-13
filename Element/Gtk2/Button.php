<?php
/**
 * A Structures_Form element for a generic button.
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
class Structures_Form_Element_Gtk2_Button extends GtkButton implements Structures_Form_ElementInterface {

    /**
     * The Structures_Form object.
     *
     * @access public
     * @var    object
     */
    public $form;

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
    public function __construct(Structures_Form $form, $label = '',
                                $text = Gtk::STOCK_OK, $callback = null,
                                $size = null, $style = null)
    {
        // Call the parent constructor.
        parent::__construct();

        // Set the form object.
        $this->form = $form;

        // Set the label.
        $this->setLabel($label);
        
        // Set the text if some was given.
        if (!is_null($text)) {
            $this->setValue($text);
        }

        // Set the size if it was given.
        if (is_array($size)) {
            $this->set_size_request($size[0], $size[1]);
        }

        // Set the style if it was given.
        if ($style instanceof GtkStyle) {
            $this->set_style($style);
        }

        // Connect to the form's sumbit method.
        if (is_callable($callback)) {
            $this->connect_simple('clicked', $callback);
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
        if (is_string($value) || is_null($value)) {
            $this->set_label($value);
            $this->set_use_stock(true);
            return true;
        } else {
            return false;
        }
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
     * @access public
     * @return string
     */
    public function getValue()
    {
        return $this->get_label();
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
     * It is highly unlikely that a user really wants to clear the value of a
     * button. Therefore, this method has been deactivated. It will always 
     * return true. If the user really wants to clear the value, they can pass
     * null to setValue()
     *
     * @access public
     * @return boolean true at all times.
     */
    public function clearValue()
    {
        return true;
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
        return 'button';
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
     * Unfreezes the element so that its value may not be changed.
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
        $this->set_data('label', $label);
    }

    /**
     * Returns the GtkLabel that identifies the element.
     *
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return $this->get_data('label');
    }

    /**
     * Adds an event handler for the element.
     *
     * To simplify things, all signal handlers are created with connect_simple.
     * If you want the regular connect arguments to be passed, you must pass
     * them in your code. 
     *
     * @access public
     * @param  string  $eventName The name of the event.
     * @param  mixed   $callback  The callback to call when the event occurs.
     * @return integer An identifier for the callback.
     */
    public function addEventHandler($eventName, $callback)
    {
        return $this->connect_simple($eventName, $callback);
    }
}
?>