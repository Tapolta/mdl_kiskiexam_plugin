<?php
namespace local_kiskiexam;

defined('MOODLE_INTERNAL') || die();

class observer {
    public static function quiz_viewed(\mod_quiz\event\course_module_viewed $event) {
        $quizid = $event->objectid;
    }
}
