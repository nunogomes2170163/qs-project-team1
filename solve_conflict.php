<?php

require_once("helper.php");

try {
    session_start();
    resolveConflict($_POST);
} catch (Exception $e) {
    header('Bad Request', true, 400);
    echo 'Something went wrong';
}