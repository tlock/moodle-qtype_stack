<?php
// This file is part of Stack - http://stack.bham.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.


/**
 * A basic text-field input.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_dragmath_input extends stack_input {

    public function render(stack_input_state $state, $fieldname, $readonly) {
        global $CFG, $PAGE;

        $attributes = array(
            'type' => 'text',
            'name' => $fieldname,
            'id'   => $fieldname,
        );

        if ($this->is_blank_response($state->contents)) {
            $value = $this->parameters['syntaxHint'];
        } else {
            $value = $this->contents_to_maxima($state->contents);
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
            $attributes['value']    = $value;
            return html_writer::empty_tag('input', $attributes);
        } else {

            $box = "<script language=\"JavaScript\">
function stack_dragmath_submit(id) {
    var ans;
    ans = document.getElementById(\"dragmath_\"+id).getMathExpression();
    document.getElementById(id).value = ans;
}
</script>";
            $dragmathurl = $CFG->wwwroot . '/question/type/stack/stack/input/dragmath/applet';
            $box .= "<applet name=\"dragmath_$fieldname\" id=\"dragmath_$fieldname\" codebase=\"$dragmathurl\"
                         code=\"Display.MainApplet.class\" archive=\"DragMath.jar\"
                         width=540 height=332  mayscript=\"mayscript\" style=\"border:2px solid black;\">
                         <param id=\"dragmath_$fieldname\" name=openWithExpression value=\"$value\" >
                         <param name=hideMenu value= \"true\" >
                         <param name=customToolbar value= \"0 3 4 | 5 6 7\" >
                         <param name=language value=\"".current_language()."\">
                         <param name=outputFormat value=\"Maxima\">
                 To use this page you need a Java-enabled browser. Download the latest Java plug-in from <a href=\"http://www.java.com\">Java.com</a>
                 </applet></div>";
            $box .= "\n<input type=\"button\" value=\"Done Editing\" id=\"edit_$fieldname\" onclick=\"stack_dragmath_submit('$fieldname')\">";
            $box .= "\n<input type=\"input\" value=\"$value\" name=\"$fieldname\" id=\"$fieldname\">";
            return $box;
        }
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => false,
            'hideFeedback'   => true,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'allowWords'     => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => true);
    }

}
