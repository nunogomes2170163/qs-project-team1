<?php
/**
 * Created by PhpStorm.
 * User: davidalecrim
 * Date: 19/05/18
 * Time: 11:22
 */

try {
    session_start();
    $_SESSION["filterState"] = $_POST;
} catch (Exception $e) {
    header('Bad Request', true, 400);
    echo 'Something went wrong';
}
