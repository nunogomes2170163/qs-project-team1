<?php
/**
 * Created by PhpStorm.
 * User: Joao Caroco
 * Date: 10/04/2018
 * Time: 14:22
 */

require_once("helper.php");

try {
    session_start();
    $_SESSION['filterState'] = ["linkedin" => ["on" => "false", "source" => "linkedin"], "facebook" => ["on" => "false", "source" => "facebook"]];
    header('Location: get_contacts.php');
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}