<?php
/**
 * Created by PhpStorm.
 * User: Joao Caroco
 * Date: 10/04/2018
 * Time: 14:23
 */
require_once("helper.php");

try {
    $json_contacts = [];
    $url = 'http://contactsqs.apphb.com/Service.svc/rest/contacts';
    $response = callAPI('GET',$url, '');
    $json_contacts = json_decode($response, true);

} catch (Exception $e) {
    header('Unauthorized', true, 401);
    echo 'Something went wrong';
}

