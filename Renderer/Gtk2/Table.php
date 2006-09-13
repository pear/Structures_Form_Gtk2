<?php
/**
 * A Structures_Form renderer that organizes elements into a GtkTable.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'Structures/Form/RendererInterface.php';
class Structures_Form_Renderer_Gtk2_Table extends GtkTable implements Structures_Form_RendererInterface {
    
    /**
     * The Structures_Form.
     *
     * @access public
     * @var    object
     */
    public $form;

    /**
     * A note identifying the required field marker.
     * The renderer should prepend the required symbol to the note.
     *
     * @access protected
     * @var    string
     */
    protected $requiredNote = ' denotes required field';

    /**
     * A symbol identifying the required fields.
     * The renderer should provide some sort of formatting to make the symbol
     * standout.
     *
     * @access protected
     * @var    string
     */
    protected $requiredSymbol = '*';

    /**
     * The form elements to display.
     *
     * @access public
     * @var    array
     */
    public $elements = array();

    /**
     * The form errors to display.
     *
     * @access public
     * @var    array
     */
    public $errors = array();

    /**
     * The widget holding the current errors.
     *
     * @access public
     * @var    object A GtkVBox
     */
    public $errorBox;

    /**
     * The widget holding the buttons.
     *
     * @access public
     * @var    object A GtkHButtonBox
     */
    public $buttonBox;

    /**
     * Constructor. Calls parent constructor and creates an error box.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Call the parent constructor.
        parent::__construct(1, 1);

        // Create the error box.
        $this->errorBox = new GtkVBox();

        // Create the button box.
        $this->buttonBox = new GtkHButtonBox();
    }

    /**
     * Sets the form.
     *
     * @access public
     * @param  object $form The form.
     * @return void
     */
    public function setForm(Structures_Form $form)
    {
        $this->form = $form;
    }

    /**
     * Sets the elements that make up the form.
     *
     * The array passed in will be of the form: array(<name> => <element>)
     *
     * @access public
     * @param  array  $elements The elements that make up the form.
     * @return void
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * Sets the errors to be displayed.
     *
     * @access public
     * @param  array  $errors An array of error strings.
     * @return void
     */
    public function setErrors($errors)
    {
        // Set the errors array.
        $this->errors = $errors;

        // Update the displayed errors.
        $this->updateErrors();
    }

    /**
     * Updates the errors in the errorBox.
     *
     * @access protected
     * @return void
     */
    protected function updateErrors()
    {
        // First remove all children.
        foreach ($this->errorBox->get_children() as $child) {
            $this->errorBox->remove($child);
        }
        
        // Next create labels for each error and wrap them in Pango markup.
        foreach ($this->errors as $error) {
            $string = '<span color="#CC0000">' . $error . '</span>';
            $label  = new GtkLabel($string);

            // Tell the label to use markup.
            $label->set_use_markup(true);
            
            // Pack the label into the vBox.
            $this->errorBox->pack_start($label);
        }
        // Show all errors.
        $this->errorBox->show_all();
    }

    /**
     * Returns the rendered form.
     *
     * This method should return something inline with the intent of the
     * renderer even if there are no elements in the form. For example, if the
     * renderer is a GtkTable renderer, this method should return a GtkTable
     * no matter what. Of course, it may throw an exception if needed but 
     * should not return anything other than a GtkTable. Not even null or void.
     *
     * @access public
     * @return object A GtkTable containing the form.
     */
    public function render()
    {
        // Remove any children that are currently in the table.
        foreach ($this->get_children() as $child) {
            $this->remove($child);
        }

        // Attach the errors.
        $this->attachErrors();

        // Add the elements to the table.
        $lastRow = $this->attachElements();

        // Attach the required note.
        $this->attachRequiredNote($lastRow);

        // Return the table.
        return $this;
    }

    /**
     * Attachs the error box to the table.
     *
     * @access protected
     * @return void
     */
    protected function attachErrors()
    {
        $this->attach($this->errorBox, 0, 2, 0, 1);
    }

    /**
     * Attaches the form elements to the table. Also attaches a required note.
     *
     * Buttons are always added at the bottom in a button box unless the button
     * has a label. If the button has a label, it is treated like a normal
     * element.
     * 
     * @access protected
     * @reutrn integer   The next row in the table.
     */
    protected function attachElements()
    {
        // Loop through the elements.
        // Start at row 1 because the errors are in row 0.
        $row     = 1;

        // Save buttons for the end.
        $buttons = array();

        foreach ($this->elements as $element) {
            // Hold of on buttons if there is no label.
            if ($element instanceof GtkButton && 
                !strlen($element->getLabel())
                ) {
                $buttons[] = $element;
            } elseif ($element instanceof GtkWidget) {
                // Only attach GtkWidgets.
                $this->attachElement($element, $row++);
            }
        }

        // Pack the buttons into the button box.
        $this->attachButtons($buttons, $row++);

        return $row;
    }

    /**
     * Attaches the given element in the given row.
     *
     * Each element is attached with the label in the left column and the
     * widget in the right hand column.
     *
     * @access protected
     * @param  object    $element The element to attach.
     * @param  integer   $row     The row to attach the element in.
     * @return void
     */
    protected function attachElement($element, $row)
    {
        // Create a label for the element.
        $string = $element->getLabel();

        // Check to see if the element is required.
        require_once 'Structures/Form.php';
        if ($this->form instanceof Structures_Form &&
            $this->form->isRequired($element->getName())
            ) {
            // Append a required marker.
            $string = $this->createRequiredMarker() . $string;
        }

        if (!empty($string)) {
            // Create the label widget.
            $label = new GtkLabel($string);
            
            // Tell the label to use Pango markup.
            $label->set_use_markup(true);
            
            // Add the label to a GtkAlignment with left alignment.
            $align = new GtkAlignment(1, .5, 0, 0);
            $align->add($label);
            
            // Attach the alignment.
            $this->attach($align, 0, 1, $row, $row + 1,
                          Gtk::EXPAND|Gtk::FILL, 0, 2, 2);
        }
        
        // Attach the element in the other column.
        $align = new GtkAlignment(0, .5, 0, 0);
        $align->add($element);
        $this->attach($align, 1, 2, $row, $row + 1,
                      Gtk::EXPAND|Gtk::FILL, 0, 2, 2);
    }

    /**
     * Packs the buttons into the button box.
     * 
     * @access protected
     * @param  array     $buttons An array of GtkButtons
     * @param  integer   $row     The row to attach the button box to.
     * @return void
     */
    protected function attachButtons($buttons, $row)
    {
        // First remove any existing buttons.
        foreach ($this->buttonBox->get_children() as $child) {
            $this->buttonBox->remove($child);
        }
        
        // Next pack the buttons.
        foreach ($buttons as $button) {
            $this->buttonBox->pack_start($button);
        }

        // Attach the button box to the table.
        $this->attach($this->buttonBox, 0, 2, $row, $row + 1,
                      Gtk::EXPAND|Gtk::FILL, 0, 2, 2);
    }

    /**
     * Attachs the required note to the given row.
     *
     * @access protected
     * @param  integer $row The row to attach the note to.
     * @return void
     */
    protected function attachRequiredNote($row)
    {
        // If the required note is empty, don't bother.
        if (empty($this->requiredNote)) {
            return;
        }

        // Append a marker to the required string.
        $note = $this->createRequiredMarker() . $this->requiredNote;

        // Create a label for the note.
        $label = new GtkLabel($note);

        // Tell the label to use Pango markup.
        $label->set_use_markup(true);
        
        // Create a GtkAlignment for the note.
        $align = new GtkAlignment(.5, .5, 0, 0);
        $align->add($label);
        
        // Attach the alignment.
        $this->attach($align, 0, 2, $row, $row + 1,
                      Gtk::EXPAND|Gtk::FILL, 0, 2, 2);
    }

    /**
     * Returns a required symbol string.
     * 
     * Wraps the symbol in Pango markup to make it red.
     *
     * @access public
     * @return string.
     */
    public function createRequiredMarker()
    {
        return '<span color="#CC0000">' . $this->requiredSymbol . '</span>';
    }
    
    /**
     * Sets the string to be used as the note indicating what the required 
     * symbol means.
     *
     * The required note does not include the required symbol. It is up to the
     * renderer to append or prepend the required symbol in a way that makes
     * sense for the rendered output.
     *
     * The required note is controlled by the form to maintain consistency when
     * a single form is rendered in different ways.
     * 
     * @access public
     * @param  string $note The required note.
     * @return void
     */
    public function setRequiredNote($note)
    {
        $this->requiredNote = $note;
    }

    /**
     * Sets the string to be used as the note indicating what the required 
     * symbol means.
     *
     * The required note does not include the required symbol. It is up to the
     * renderer to append or prepend the required symbol in a way that makes
     * sense for the rendered output.
     *
     * The required symbol is controlled by the form to maintain consistency
     * when a single form is rendered in different ways.
     * 
     * @access public
     * @param  string $symbol The required symbol.
     * @return void
     */
    public function setRequiredSymbol($symbol)
    {
        $this->requiredSymbol = $symbol;
    }

    /**
     * Returns the current required note.
     *
     * @access public
     * @return string
     */
    public function getRequiredNote()
    {
        return $this->requiredNote;
    }

    /**
     * Returns the current required symbol.
     *
     * @access public
     * @return string
     */
    public function getRequiredSymbol()
    {
        return $this->requiredSymbol;
    }
}
?>