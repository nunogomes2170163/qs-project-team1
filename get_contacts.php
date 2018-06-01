<?php

require_once("helper.php");

try {
    session_start();
    getAllContacts();
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}
