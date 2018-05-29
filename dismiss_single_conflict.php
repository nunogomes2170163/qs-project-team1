<?php

require_once("helper.php");

try {
    session_start();
    dismissContactConflicts($_GET["guids"]);
    header("Refresh: 0; url=resolve_conflicts.php");
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}