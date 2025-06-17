<?php

require('../../config.php');
require_login();

$PAGE->set_url(new moodle_url('/local/kiskiexam/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('KiskiExam');
$PAGE->set_heading('KiskiExam');

echo $OUTPUT->header();
echo $OUTPUT->heading('Selamat datang di KiskiExam!');
echo $OUTPUT->footer();
