<?php

defined('MOODLE_INTERNAL') || die();

function local_kiskiexam_coursemodule_standard_elements($formwrapper, MoodleQuickForm $mform) {
    global $DB;

    $quizid = 0;
    $cm = $formwrapper->get_coursemodule();
    if ($cm && !empty($cm->instance)) {
        $quizid = $cm->instance;
    } else {
        $current = $formwrapper->get_current();
        if ($current && !empty($current->id)) {
            $quizid = $current->id;
        }
    }

    $defaults = [
        'kiskiexam_enable' => 0,
        'kiskiexam_password' => '',
        'kiskiexam_showbattery' => 0,
        'kiskiexam_showwifi' => 0,
    ];

    if ($quizid && $DB->get_manager()->table_exists('local_kiskiexam_data')) {
        $rec = $DB->get_record('local_kiskiexam_data', ['quizid' => $quizid]);
        if ($rec) {
            $defaults = [
                'kiskiexam_enable' => $rec->enable,
                'kiskiexam_password' => $rec->password,
                'kiskiexam_showbattery' => $rec->showbattery,
                'kiskiexam_showwifi' => $rec->showwifi,
            ];
        }
    }

    $mform->addElement('header', 'kiskiexam_header', get_string('pluginname', 'local_kiskiexam'));

    $mform->addElement('advcheckbox', 'kiskiexam_enable', get_string('enable', 'local_kiskiexam'));
    $mform->setDefault('kiskiexam_enable', $defaults['kiskiexam_enable']);

    $mform->addElement('text', 'kiskiexam_password', get_string('password', 'local_kiskiexam'));
    $mform->setType('kiskiexam_password', PARAM_TEXT);
    $mform->setDefault('kiskiexam_password', $defaults['kiskiexam_password']);
    $mform->hideIf('kiskiexam_password', 'kiskiexam_enable', 'notchecked');

    $mform->addElement('advcheckbox', 'kiskiexam_showbattery', get_string('showbattery', 'local_kiskiexam'));
    $mform->setDefault('kiskiexam_showbattery', $defaults['kiskiexam_showbattery']);
    $mform->hideIf('kiskiexam_showbattery', 'kiskiexam_enable', 'notchecked');

    $mform->addElement('advcheckbox', 'kiskiexam_showwifi', get_string('showwifi', 'local_kiskiexam'));
    $mform->setDefault('kiskiexam_showwifi', $defaults['kiskiexam_showwifi']);
    $mform->hideIf('kiskiexam_showwifi', 'kiskiexam_enable', 'notchecked');
}

function local_kiskiexam_coursemodule_edit_post_actions($data, $course) {
    global $DB;

    if (
        !isset($data->modulename) || $data->modulename !== 'quiz' ||
        !isset($data->instance) || !$data->instance
    ) {
        return $data;
    }

    $quizid = $data->instance;

    $newdata = (object)[
        'quizid' => $quizid,
        'enable' => !empty($data->kiskiexam_enable) ? 1 : 0,
        'password' => $data->kiskiexam_password ?? '',
        'showbattery' => !empty($data->kiskiexam_showbattery) ? 1 : 0,
        'showwifi' => !empty($data->kiskiexam_showwifi) ? 1 : 0,
    ];

    $existing = $DB->get_record('local_kiskiexam_data', ['quizid' => $quizid]);
    if ($existing) {
        $newdata->id = $existing->id;
        $DB->update_record('local_kiskiexam_data', $newdata);
    } else {
        $DB->insert_record('local_kiskiexam_data', $newdata);
    }

    return $data;
}


function local_kiskiexam_after_update_module($moduleinfo, $course) {
    global $DB;

    if (!isset($moduleinfo->modulename) || $moduleinfo->modulename !== 'quiz') {
        return;
    }

    $quizid = $moduleinfo->instance ?? 0;
    if (!$quizid) {
        return;
    }

    $newdata = (object)[
        'quizid' => $quizid,
        'enable' => !empty($moduleinfo->kiskiexam_enable) ? 1 : 0,
        'password' => $moduleinfo->kiskiexam_password ?? '',
        'showbattery' => !empty($moduleinfo->kiskiexam_showbattery) ? 1 : 0,
        'showwifi' => !empty($moduleinfo->kiskiexam_showwifi) ? 1 : 0,
    ];

    $existing = $DB->get_record('local_kiskiexam_data', ['quizid' => $quizid]);
    if ($existing) {
        $newdata->id = $existing->id;
        $DB->update_record('local_kiskiexam_data', $newdata);
    } else {
        $DB->insert_record('local_kiskiexam_data', $newdata);
    }
}

function local_kiskiexam_hook_classes(): array {
	error_log('[KISKI] User agent tidak cocok');
    return [
        'core\hook\before_http_request' => \local_kiskiexam\hook\before_http_request::class,
    ];
}

