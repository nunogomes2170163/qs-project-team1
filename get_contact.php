<?php
/**
 * Created by PhpStorm.
 * User: Joao Caroco
 * Date: 10/04/2018
 * Time: 14:23
tesdhvfasgbfasjkdfbasjfnZJLK
 */

require_once("helper.php");

if( $_GET["guid"] )
{
    getContact( $_GET["guid"]);
}else{
    header('Contact not found', true, 404);
    echo 'Contact not found';
}
