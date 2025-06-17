<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_quiz\event\course_module_viewed',
        'callback'    => '\local_kiskiexam\observer::quiz_viewed',
        'includefile' => null,
        'priority'    => 1000,
        'internal'    => false,
    ],
];
