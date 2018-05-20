<?php
/**
 * Created by PhpStorm.
 * User: davidalecrim
 * Date: 20/05/18
 * Time: 12:07
 */

require_once("helper.php");

try {
    session_start();
    getAllContactsAndDisplayConflicts();
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}