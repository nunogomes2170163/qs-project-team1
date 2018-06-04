<?php

require_once("helper.php");

try {
    session_start();
    getEnvolvedContactsAndDisplayConflict();
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}