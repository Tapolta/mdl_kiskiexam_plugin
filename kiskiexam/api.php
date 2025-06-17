<?php

define('NO_MOODLE_COOKIES', true);
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');

header('Content-Type: application/json');

$cmid = optional_param('cmid', 0, PARAM_INT);

if (empty($cmid)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing cmid']);
    exit;
}

$cm = get_coursemodule_from_id('quiz', $cmid, 0, false, MUST_EXIST);
if (!$cm || empty($cm->instance)) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid cmid or quiz not found']);
    exit;
}

$quizid = (int)$cm->instance;

global $DB;
$data = $DB->get_record('local_kiskiexam_data', ['quizid' => $quizid]);

if (!$data) {
    http_response_code(404);
    echo json_encode(['error' => 'No configuration found for this quiz']);
    exit;
}

echo json_encode([
    'enable'      => (int)($data->enable ?? 0),
    'password'    => (string)($data->password ?? ''),
    'showbattery' => (int)($data->showbattery ?? 0),
    'showwifi'    => (int)($data->showwifi ?? 0),
]);
exit;

