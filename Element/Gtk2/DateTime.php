<?php
/**
 * A Structures_Form element for a set of date and time dropdown boxes.
 *
 * This element is a composite element made of several Gtk2TextSelect elements.
 *
 * This class implement Structures_Form_ElementInterface.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
class Structures_Form_Element_Gtk2_DateTime extends GtkHBox implements Structures_Form_ElementInterface {

    /**
     * Constants for the element segments.
     *
     * @const
     */
    const ELEMENT_MONTH  = 'month';
    const ELEMENT_DAY    = 'day';
    const ELEMENT_YEAR   = 'year';
    const ELEMENT_HOUR   = 'hour';
    const ELEMENT_MINUTE = 'minute';
    const ELEMENT_SECOND = 'seconds';
    const ELEMENT_AMPM   = 'ampm';

    /**
     * Default values
     *
     * @const
     */
    const DEFAULT_DURATION   = 10;
    const DEFAULT_INCREMENTS = 10;

    /**
     * The Structures_Form object.
     *
     * @access public
     * @var    object
     */
    public $form;

    /**
     * The month element.
     * 
     * @access public
     * @var    object
     */
    public $month;

    /**
     * The day element.
     * 
     * @access public
     * @var    object
     */
    public $day;

    /**
     * The year element.
     * 
     * @access public
     * @var    object
     */
    public $year;
    /**
     * The hour element.
     * 
     * @access public
     * @var    object
     */
    public $hour;

    /**
     * The minute element.
     * 
     * @access public
     * @var    object
     */
    public $minute;

    /**
     * The second element.
     * 
     * @access public
     * @var    object
     */
    public $second;

    /**
     * The AM/PM element.
     * 
     * @access public
     * @var    object
     */
    public $ampm;

    /**
     * The increments to display for minutes.
     *
     * @access public
     * @var    integer
     */
    public $minuteIncrements;

    /**
     * Constructor.
     *
     * @access public
     * @param  object  $form      The Structures_Form.
     * @param  string  $label     The string to be used as the label.
     * @param  string  $date      The default date (optional).
     * @param  array   $order     The elements to display and the order to
     *                            display them. (optional).
     * @param  integer $yearStart The first year for the year element
     *                            (optional).
     * @param  integer $duration  The number of years to show (optional).
     * @param  array   $size      The height and width of the entry (optional).
     * @param  object  $style     The GtkStyle to apply to the elements
     *                            (optional).
     * @return void
     */
    public function __construct(Structures_Form $form, $label, $date = null,
                                $order = null, $yearStart = null,
                                $duration = null, $minuteIncrements = null,
                                $size = null, $style = null)
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

        // Set the display elements and order.
        if (!is_array($order)) {
            $order = array(self::ELEMENT_MONTH, 
                           self::ELEMENT_DAY, 
                           self::ELEMENT_YEAR,
                           self::ELEMENT_HOUR,
                           self::ELEMENT_MINUTE,
                           self::ELEMENT_SECOND,
                           self::ELEMENT_AMPM
                           );
        }

        // Create the individual elements.
        if (is_null($yearStart)) {
            $yearStart = date('Y');
        }

        if (is_null($duration) || !is_numeric($duration)) {
            $duration = self::DEFAULT_DURATION;
        }

        if (is_null($minuteIncrements) || !is_numeric($minuteIncrements) ||
            $minuteIncrements < 1) {
            $minuteIncrements = self::DEFAULT_INCREMENTS;
        }
        $this->minuteIncrements = $minuteIncrements;
            
        $this->createElements($yearStart, $duration, in_array(self::ELEMENT_AMPM, $order));

        // Set the date. Default to now.
        if (is_null($date)) {
            $date = time();
        }
        $this->setValue($date);

        // Pack the elements in the order they should appear.
        foreach ($order as $element) {
            $this->pack_start($this->$element, false, false, 3);
        }
    }

    /**
     * Creates the individual date elements.
     * 
     * This element is made up of month, day and year elements.
     *
     * @access protected
     * @param  integer   $yearStart  The first year to show.
     * @param  integer   $duration   The number of years to show.
     * @param  boolean   $twelveHour true to use 12 hour format.
     * @return void
     */
    protected function createElements($yearStart, $duration, $twelveHour = false)
    {
        require_once 'Structures/Form/Element/Gtk2/TextSelect.php';

        // Create the month element.
        $this->month = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                  null
                                                                  );
        
        // Add the months.
        for ($i = 1; $i <= 12; ++$i) {
            // Use date to get local month names.
            $this->month->addOption($i, date('F', mktime(0,0,0,$i,1,2000)));
        }

        // Create the day element.
        $this->day = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                null
                                                                );
        
        // Add the days.
        for ($i = 1; $i <= 31; ++$i) {
            $this->day->addOption($i, $i);
        }

        // Create the year element.
        $this->year = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                 null
                                                                 );
        
        // Add the years.
        for ($i = 0; $i < $duration; ++$i) {
            $this->year->addOption($yearStart + $i, $yearStart + $i);
        }

        // Create the hour element.
        $this->hour = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                  null
                                                                  );
        
        // Add the hours.
        $start = 0;
        $limit = 23;
        if ($twelveHour) {
            $start = 1;
            $limit = 12;
        }

        for ($i = $start; $i <= $limit; ++$i) {
            // Figure out what to show.
            $this->hour->addOption(str_pad($i, 2, '0', STR_PAD_LEFT), $i);
        }

        // Create the minute element.
        $this->minute = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                    null
                                                                    );
        
        // Add the days.
        for ($i = 0; $i <= 59; $i += $this->minuteIncrements) {
            $mins = str_pad($i, 2, '0', STR_PAD_LEFT);
            $this->minute->addOption($mins, $mins);
        }

        // Create the seconds element.
        $this->second = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                     null
                                                                     );
        
        // Add the seconds.
        for ($i = 0; $i <= 59; ++$i) {
            $this->second->addOption(str_pad($i, 2, '0', STR_PAD_LEFT), $i);
        }

        // Create the AMPM element.
        $this->ampm = new Structures_Form_Element_Gtk2_TextSelect($this->form,
                                                                  null
                                                                  );

        // Add the options.
        $this->ampm->addOption(0, 'AM');
        $this->ampm->addOption(1, 'PM');
    }

    /**
     * Sets an element's value.
     *
     * This method should set the value of the widget not just set some data
     * that is retrieved later. If the widget is a GtkEntry, this method should
     * call set_text(). If the widget is a GtkComboBox, this method should set
     * the active row.
     *
     * To set the value of this element, we must set the value of the three
     * composite elements. To do this, we first turn the timestamp into a date
     * string and then explode on '-'. This is faster than calling date() three
     * different times.
     *
     * @access public
     * @param  string  $value The text to put in the entry.
     * @return boolean true if the value was changed.
     */
    public function setValue($value)
    {
        // Make sure the value is a number
        if (!is_numeric($value)) {
            $value = strtotime($value);
        }

        $success = true;

        // Turn the value into a string representation.
        $date = date('Y-m-d', $value);
        
        // Now break the date string up into pieces.
        list($year, $month, $day) = explode('-', $date);

        // Finally set the three elements.
        $success = $success && $this->month->setValue($month);
        $success = $success && $this->day->setValue($day);
        $success = $success && $this->year->setValue($year);

        // Turn the value into a string representation for the time part.
        $time = date('H-i-s', $value);
        
        // Now break the date string up into pieces.
        list($hour, $minute, $second) = explode('-', $time);

        // Finally set the four time elements.
        $success = $success && $this->hour->setValue($hour);
        $success = $success && $this->minute->setValue($minute);
        $success = $success && $this->second->setValue($second);
        $success = $success && $this->ampm->setValue((int)($hour > 11));

        return $success;
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
     * @return integer
     */
    public function getValue()
    {
        return mktime($this->hour->getValue(),
                      $this->minute->getValue(),
                      $this->second->getValue(),
                      $this->month->getValue(),
                      $this->day->getValue(),
                      $this->year->getValue()
                      );
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
     * It may not be possible to clear this element type. If the children
     * cannot be cleared, this element cannot be cleared. 
     *
     * @access public
     * @return boolean true if the value was cleared.
     */
    public function clearValue()
    {
        return ($this->month->clearValue()  &&
                $this->day->clearValue()    &&
                $this->year->clearValue()   &&
                $this->hour->clearValue()   &&
                $this->minute->clearValue() &&
                $this->second->clearValue()
                );
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
        return 'datetime';
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
     * @access public
     * @param  string  $eventName The name of the event.
     * @param  mixed   $callback  The callback to call when the event occurs.
     * @return array   An array of identifiers, one for each piece.
     */
    public function addEventHandler($eventName, $callback)
    {
        return array($this->month->addEventHandler($eventName,  $callback),
                     $this->day->addEventHandler($eventName,    $callback),
                     $this->year->addEventHandler($eventName,   $callback),
                     $this->hour->addEventHandler($eventName,   $callback),
                     $this->minute->addEventHandler($eventName, $callback),
                     $this->second->addEventHandler($eventName, $callback)
                     );
    }
}
?>