<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_kiskiexam_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025061410) {

        $table = new xmldb_table('local_kiskiexam_data');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quizid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('showbattery', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('showwifi', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('quizid_unique', XMLDB_KEY_UNIQUE, ['quizid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025061410, 'local', 'kiskiexam');
    }

    return true;
}
