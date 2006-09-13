<?php
// A dummy function to show the submitted values.
function dumpValues($values)
{
    var_dump($values);
}

// Create a window and set it up to close cleanly.
$window = new GtkWindow();
$window->connect_simple('destroy', array('Gtk', 'main_quit'));

// Create a form.
require_once 'Structures/Form.php';
$form = new Structures_Form('dumpValues');

// Register the Gtk2ElementSet.
$form->registerElementSet('Gtk2ElementSet');

// Add some elements.
$form->addElement('text',     'username', 'Username:');
$form->addElement('password', 'password', 'Password:');
$form->addElement('cancel',   'cancel');
$form->addElement('submit',   'submit');

// Make username required.
$form->addRule('username', 'required');

// Create a group for authentication.
$form->addElement('frame', 'authentication', 'Authentication');
$form->addElementToGroup('username', 'authentication');
$form->addElementToGroup('password', 'authentication');

// Add a text select with user types.
$userTypes = $form->addElement('textselect', 'usertypes', 'User Type');
$userTypes->addOption('lead',   'Lead');
$userTypes->addOption('helper', 'Helper');
$userTypes->setValue('lead');

// Add a text area.
$form->addElement('textarea', 'big text block', 'Some Text',
                  'Some default text.', array(200, 100)
                  );

// Add a file selector.
$form->addElement('file', 'file', 'Select a file:',
                  getcwd(), Gtk::FILE_CHOOSER_ACTION_OPEN
                  );

// Render the form and add it to the window.
$window->add($form->render());

// Show the window and start the main loop.
$window->show_all();
Gtk::main();
?>