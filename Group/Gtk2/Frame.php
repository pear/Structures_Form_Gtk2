<?php
/**
 * A Structures_Form group based on GtkFrame.
 *
 * This group simply puts its element inside of a GtkFrame. Within the frame 
 * the elements are organized using a renderer. By default the form's default
 * renderer will be used. For simplicity of the end user, the form is rendered
 * every time an element is added or removed.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'Structures/Form/ElementInterface.php';
require_once 'Structures/Form/GroupInterface.php';
class Structures_Form_Group_Gtk2_Frame extends GtkFrame implements Structures_Form_ElementInterface, Structures_Form_GroupInterface {

    /**
     * The Structures_Form object.
     *
     * @access public
     * @var    object
     */
    public $form;

    /**
     * Elements that are members of this group.
     *
     * @access public
     * @var    array
     */
    public $elements = array();

    /**
     * The renderer for the element.
     * 
     * @access public
     * @var    object
     */
    public $renderer;

    /**
     * Constructor.
     *
     * @access public
     * @param  object $form     The form.
     * @param  string $label    The string to be used as the label (optional).
     * @param  array  $size     The height and width of the entry (optional).
     * @param  object $style    The GtkStyle to apply to the entry (optional).
     * @param  object $renderer A Structures_Form renderer for the frame
     *                          contents (optional).
     * @return void
     */
    public function __construct($form, $label = '',
                                $size = null, $style = null, $renderer = null)
    {
        // Call the parent constructor.
        parent::__construct();

        // Set the form object.
        $this->form = $form;

        // Set the label.
        $this->setLabel($label);
        
        // Set the size if it was given.
        if (is_array($size)) {
            $this->set_size_request($size[0], $size[1]);
        }

        // Set the style if it was given.
        if ($style instanceof GtkStyle) {
            $this->set_style($style);
        }

        // Set the renderer.
        $interface = Structures_Form::RENDERER_INTERFACE;
        require_once Structures_Form::RENDERER_INTERFACE_PATH;
        if ($renderer instanceof $interface) {
            $this->setRenderer($renderer);
        } else {
            $this->setDefaultRenderer();
        }
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
        return 'frame';
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
     * Sets a renderer object.
     *
     * Renderers must implement Structures_Form_RendererInterface. This helps
     * to ensure consistency among the API and avoid any fatal errors. A
     * renderer is used to position and display the form elements within a
     * container widget. 
     *
     * Unlike rules and elements, renderers are created on their own (not 
     * through a form method). This is because they do not need to know 
     * thing about the form at construction time and the form does not need to
     * know anything about the renderer until the form is to be displayed.
     *
     * A form may only have one renderer at a time. Setting a second renderer
     * will overwrite the first. If no renderer is set, the default renderer
     * will be used.
     *
     * @access public
     * @param  object $renderer An object that implements
     *                          Structures_Form::RENDERER_INTERFACE
     * @return void
     * @throws Structures_Form_Gtk2_Exception
     */
    public function setRenderer($renderer)
    {
        // Make sure that the renderer is an object and that it implements the
        // needed interface.
        $interface = Structures_Form::RENDERER_INTERFACE;
        require_once Structures_Form::RENDERER_INTERFACE_PATH;
        if (!is_object($renderer) ||
            !$renderer instanceof $interface
            ) {
            require_once 'Structures/Form/Gtk2/Exception.php';
            throw new Structures_Form_Gtk2_Exception(Structures_Form::$errorMsgs[Structures_Form::ERROR_LACKSINTERFACE_RENDERER],
                                                     Structures_Form::ERROR_LACKSINTERFACE_RENDERER
                                 );
        }

        // Set the renderer.
        $this->renderer = $renderer;

        // Set the form for the renderer.
        $this->renderer->setForm($this->form);
    }

    /**
     * Creates a default renderer and sets it as the current renderer.
     *
     * An exception may be thrown by setRenderer. It will pass through to the
     * calling function.
     *
     * @access protected
     * @return void
     */
    protected function setDefaultRenderer()
    {
        // Get a default renderer from the form.
        $obj = $this->form->getDefaultRenderer();

        // We don't want required notes to appear.
        $obj->setRequiredNote(NULL);

        // Set the renderer.
        $this->setRenderer($obj);
    }

    /**
     * Renders the contents of the frame.
     *
     * Passes the elements, required note and required symbol to the renderer
     * and then calls renderer(). 
     *
     * Pulling elements out of a renderer is a pain in the ass. Trying to find
     * the correct widget (or parent widget) can be nearly impossible.
     * Therefore, even if you remove a widget from the form, it will still 
     * appear if the widget was removed after the form was rendered. 
     *
     * @access public
     * @return object A container widget holding the form.
     * @throws Structures_Form_Gtk2_Exception
     */
    public function render()
    {
        // Check to see if a renderer has been set.
        if (empty($this->renderer)) {
            // Try to create a default renderer.
            try {
                $this->setDefaultRenderer();
            } catch (Exception $e) {
                // Set a prettier error message but keep the same code.
                require_once 'Structures/Form/Gtk2/Exception.php';
                throw new Structures_Form_Gtk2_Exception(Structures_Form::$errorMsgs[Structures_Form::ERROR_RENDER_FAILURE],
                                                         $e->getCode()
                                    );
            }
        }

        // Pass the elements to the renderer.
        $this->renderer->setElements($this->getAllElements());

        // Try to renderer the form.
        try {
            $contents = $this->renderer->render();
            
            // Clear out the old contents.
            $child = $this->get_child();
            if (!is_null($child)) {
                $this->remove($child);
            }

            // Add the new contents.
            $this->add($contents);

            // Show everything.
            $this->show_all();
        } catch (Exception $e) {
            // Set a prettier error message but keep the same code.
            require_once 'Structures/Form/Gtk2/Exception.php';
            throw new Structures_Form_Gtk2_Exception(Structures_Form::$errorMsgs[Structures_Form::ERROR_RENDER_FAILURE],
                                                     $e->getCode()
                                );
        }          
    }

    /**
     * Returns an element from a group.
     *
     * This method should return the element with the given name. If there is 
     * no element with the given name, this method should return false.
     *
     * @access public
     * @param  string $name The element name.
     * @return object The element object.
     */
    public function getElement($name)
    {
        // Make sure the elment exists.
        if (!$this->elementExists($name)) {
            return false;
        }

        return $this->elements[$name];
    }

    /**
     * Returns an array containing all elements in the group.
     *
     * The array should be of the form: array(<name> => <element>)
     *
     * @access public
     * @return array
     */
    public function getAllElements()
    {
        return $this->elements;
    }

    /**
     * Returns whether or not an element with the given name exists in the
     * group.
     *
     * @access public
     * @param  string  $name The name of the element.
     * @return boolean true if the element is a member of the group.
     */
    public function elementExists($name)
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * Adds an element to the group.
     *
     * @access public
     * @param  object  $element An element object.
     * @return boolean true if the element was added.
     */
    public function addElement($element)
    {
        // Check to see if an element with the same name already exists.
        if ($this->elementExists($element->getName())) {
            return false;
        }

        // Make sure the element doesn't already exist under a different name.
        foreach ($this->getAllElements() as $elem) {
            if ($elem === $element) {
                return false;
            }
        }

        // Add the element.
        $this->elements[$element->getName()] = $element;

        // (re)Render the contents of the frame.
        $this->render();
        
        return $this->elementExists($element->getName());
    }

    /**
     * Removes an element from the group.
     *
     * This method should fail gracefully (no errors or notices) if the element
     * is not part of the group.
     *
     * @access public
     * @param  object $element The element object to remove.
     * @return void
     */
    public function removeElement($element)
    {
        // Check to see if the element is a member of the group.
        if ($this->elementExists($element->getName())) {
            $this->elements[$element->getName()] = null;
        }

        // Rerender the contents of the frame.
        $this->render();
    }

    /**
     * Renders the contents of the frame.
     *
     */

    // NOTE: The meaning of these methods has been redefined for groups!!!

    /**
     * Sets the values of the group elements.
     *
     * This method should set the value of each element in the group. This
     * should be done by calling the element's setValue() method.
     *
     * @access public
     * @param  array   $values The values to set for the group elements.
     * @return boolean true if the value was changed.
     */
    public function setValue($values)
    {
        // Keep track of whether or not any values where changed.
        $changed = false;

        // Set the value of each element.
        foreach ($this->getAllElements() as $name => $element) {
            // Check to see if there is a new value for the element.
            if (array_key_exists($name, $values)) {
                $elem = $this->getElement($name);
                // Set the value.
                if($elem->setValue($values[$name])) {
                    $changed = true;
                }
            }
        }

        return $changed;
    }

    /**
     * Returns an array of the group elements' values.
     *
     * This method should return an array of the form: array(<name> => <value>)
     * The name and value should be obtained by calling getName() and
     * getValue() respectively for each member of the group.
     *
     * @access public
     * @return array
     */
    public function getValue()
    {
        // Collect the values of all elements.
        $values = array();
        foreach ($this->getAllElements() as $name => $element) {
            $values[$name] = $element->getValue();
        }

        return $values;
    }

    /**
     * Clears the current values of the group elements.
     *
     * This method should clear the current value of each element in the group.
     * If not all values can be cleared, this method should return false. To
     * clear the elements' values, clearValue() should be called on each 
     * element.
     *
     * @access public
     * @return boolean true if the value was cleared.
     */
    public function clearValue()
    {
        // Keep track of whether or not the values where cleared.
        $cleared = true;
        
        // Loop through all of the elements and clear their values.
        foreach ($this->getAllElements() as $name => $element) {
            if (!$element->clearValue()) {
                $cleared = false;
            }
        }
        
        return $cleared;
    }

    /**
     * Freezes the elements in the group so that its value may not be changed.
     *
     * This method should call freeze() on every member of the group.
     *
     * To make life easier down the road this method should also call
     * set_data('frozen', true);
     *
     * @access public
     * @return void
     */
    public function freeze()
    {
        // Freeze all elements in the group.
        foreach ($this->getAllElements() as $name => $element) {
            $element->freeze();
        }

        // Set the data for this element.
        $this->set_data('frozen', true);
    }

    /**
     * Unfreezes the element so that its value may not be changed.
     *
     * This method should call unfreeze() on every member of the group.
     *
     * To make life easier down the road this method should also call
     * set_data('frozen', false);
     *
     * @access public
     * @return void
     */
    public function unfreeze()
    {
        // Unfreeze all elements in the group.
        foreach ($this->getAllElements() as $name => $element) {
            $element->unfreeze();
        }

        // Set the data for this element.
        $this->set_data('frozen', false);
    }

    /**
     * Returns whether or not the group is currently frozen.
     * 
     * This method should just return the value from get_data('frozen')
     *
     * @access public
     * @return boolean
     */
    public function isFrozen()
    {
        return $this->get_data('frozen');
    }

    /**
     * Sets the label that identifies the group.
     *
     * @access public
     * @param  string $label A label identifying the group.
     * @return void
     */
    public function setLabel($label)
    {
        $this->set_label($label);
    }

    /**
     * Returns the label that identifies the group.
     *
     * This group does not return an identifying label even if one is set. This
     * is because the label is automatically added as part of the frame. If you
     * want to get the label that is part of the frame use GtkFrame::get_label.
     *
     * @access public
     * @return string
     */
    public function getLabel()
    {
        // Use get_label() if you want the frame label.
        //return $this->get_label();
        return null;
    }
}
?>