<?php
/**
 * Defines the editing form for the multianswer question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * multianswer editing form definition.
 */
class question_edit_multianswer_form extends question_edit_form {

    // No question-type specific form fields.

    function set_data($question) {
        if ($question->questiontext and $question->id and $question->qtype) {

            foreach ($question->options->questions as $key => $wrapped) {
                // The old way of restoring the definitions is kept to gradually
                // update all multianswer questions
                if (empty($wrapped->questiontext)) {
                    $parsableanswerdef = '{' . $wrapped->defaultgrade . ':';
                    switch ($wrapped->qtype) {
                        case 'multichoice':
                            $parsableanswerdef .= 'MULTICHOICE:';
                            break;
                        case 'shortanswer':
                            $parsableanswerdef .= 'SHORTANSWER:';
                            break;
                        case 'numerical':
                            $parsableanswerdef .= 'NUMERICAL:';
                            break;
                        default:
                            error("questiontype $wrapped->qtype not recognized");
                    }
                    $separator= '';
                    foreach ($wrapped->options->answers as $subanswer) {
                        $parsableanswerdef .= $separator
                                . '%' . round(100*$subanswer->fraction) . '%';
                        $parsableanswerdef .= $subanswer->answer;
                        if (!empty($wrapped->options->tolerance)) {
                            // Special for numerical answers:
                            $parsableanswerdef .= ":{$wrapped->options->tolerance}";
                            // We only want tolerance for the first alternative, it will
                            // be applied to all of the alternatives.
                            unset($wrapped->options->tolerance);
                        }
                        if ($subanswer->feedback) {
                            $parsableanswerdef .= "#$subanswer->feedback";
                        }
                        $separator = '~';
                    }
                    $parsableanswerdef .= '}';
                    // Fix the questiontext fields of old questions
                    set_field('question', 'questiontext', addslashes($parsableanswerdef), 'id', $wrapped->id);
                } else {
                    $parsableanswerdef = str_replace('&#', '&\#', $wrapped->questiontext);
                }
                $question->questiontext = str_replace("{#$key}", $parsableanswerdef, $question->questiontext);
            }
        }
    }

    function qtype() {
        return 'multianswer';
    }
}
?>