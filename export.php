<?php
/**
 * Created by PhpStorm.
 * User: davidalecrim
 * Date: 22/05/18
 * Time: 11:45
 */

require_once("helper.php");

try {
    session_start();
    exportCsv();
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}