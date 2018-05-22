<?php

require_once("helper.php");

try {
    session_start();
    if ($_GET["resetData"]) resetExportData();
    else getAllContacts();
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}
