<?php
/**
 * Created by PhpStorm.
 * User: Joao Caroco
 * Date: 10/04/2018
 * Time: 14:22
 */

require_once("helper.php");

try {
    getAllContacts();
} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}