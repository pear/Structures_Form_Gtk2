<?php
/**
 * An element set for Structures_Form. This element set provides the elements
 * for PHP-GTK 2 forms.
 *
 * @author    Scott Mattocks
 * @package   Structures_Form_Gtk2
 * @license   PHP License
 * @version   @version@
 * @copyright Copyright 2006 Scott Mattocks
 */
require_once 'Structures/Form/ElementSetInterface.php';
class Structures_Form_Element_Gtk2ElementSet implements Structures_Form_ElementSetInterface {

    /**
     * Returns an array of PHP-GTK 2 element names, classes and files.
     *
     * @static
     * @access public
     * @return array
     */
    public function getElementSet()
    {
        // Create an array elements.
        $elements = array(
                          array('text',
                                'Structures_Form_Element_Gtk2_Text',
                                'Structures/Form/Element/Gtk2/Text.php'
                                ),
                          array('password',
                                'Structures_Form_Element_Gtk2_Password',
                                'Structures/Form/Element/Gtk2/Password.php'
                                ),
                          array('textselect',
                                'Structures_Form_Element_Gtk2_TextSelect',
                                'Structures/Form/Element/Gtk2/TextSelect.php'
                                ),
                          array('file',
                                'Structures_Form_Element_Gtk2_FileSelect',
                                'Structures/Form/Element/Gtk2/FileSelect.php'
                                ),
                          array('datetime',
                                'Structures_Form_Element_Gtk2_DateTime',
                                'Structures/Form/Element/Gtk2/DateTime.php'
                                ),
                          array('textarea',
                                'Structures_Form_Element_Gtk2_Textarea',
                                'Structures/Form/Element/Gtk2/Textarea.php'
                                ),
                          array('frame',
                                'Structures_Form_Group_Gtk2_Frame',
                                'Structures/Form/Group/Gtk2/Frame.php'
                                ),
                          array('spinbutton',
                                'Structures_Form_Element_Gtk2_SpinButton',
                                'Structures/Form/Element/Gtk2/SpinButton.php'
                                ),
                          array('button',
                                'Structures_Form_Element_Gtk2_Button',
                                'Structures/Form/Element/Gtk2/Button.php'
                                ),
                          array('submit',
                                'Structures_Form_Element_Gtk2_Submit',
                                'Structures/Form/Element/Gtk2/Submit.php'
                                ),
                          array('cancel',
                                'Structures_Form_Element_Gtk2_Cancel',
                                'Structures/Form/Element/Gtk2/Cancel.php'
                                ),
                          array('checkbox',
                                'Structures_Form_Element_Gtk2_CheckBox',
                                'Structures/Form/Element/Gtk2/CheckBox.php'
                                )
                          );

        return $elements;
    }

    /**
     * Returns an array of data defining the default renderer.
     *
     * @static 
     * @public
     * @return array array(class => <classname>, path => <path>);
     */
    public function getDefaultRenderer()
    {
        return  array('class' => 'Structures_Form_Renderer_Gtk2_Table',
                      'path'  => 'Structures/Form/Renderer/Gtk2/Table.php'
                      );
    }
}
?>