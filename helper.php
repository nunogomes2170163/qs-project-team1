<?php
/**
 * Created by PhpStorm.
 * User: Joao Caroco
 * Date: 10/04/2018
 * Time: 14:24
 */

function getAllContacts(){
    try {
        $linkedinSess = $_SESSION["filterState"]['linkedin'];
        $facebookSess = $_SESSION["filterState"]['facebook'];
        $linkedInChosen = $linkedinSess['on'];
        $linkedInSource = $linkedinSess['source'];
        $linkedInData = [];
        $facebookChosen = $facebookSess['on'];
        $facebookSource = $facebookSess['source'];
        $facebookData = [];
        $response = [];
        $allData = [];
        $json_contacts = [];

        if ($linkedInChosen == "true") {
            $linkedInUrl = 'http://contactsqs2.apphb.com/Service.svc/rest/contacts/bysource/' . $linkedInSource;
            $linkedInResponse = callAPI('GET',$linkedInUrl, '');
            $linkedInData = json_decode($linkedInResponse, true);
        }
        if ($facebookChosen == "true") {
            $facebookUrl = 'http://contactsqs2.apphb.com/Service.svc/rest/contacts/bysource/' . $facebookSource;
            $facebookResponse = callAPI('GET',$facebookUrl, '');
            $facebookData = json_decode($facebookResponse, true);
        }
        if ($facebookChosen == "false" && $linkedInChosen == "false") {
            //$url = 'http://contactsqs2.apphb.com/Service.svc/rest/contacts';
            //$response = callAPI('GET',$url, '');
            $allData = [];
        }
        if ($facebookChosen == "true" || $linkedInChosen == "true") $json_contacts = array_merge($linkedInData, $facebookData);
        else $json_contacts = $allData;
        include 'show_contacts.html';
        return $json_contacts;
    } catch (Exception $e) {
        header('Unauthorized', true, 401);
        echo 'Something went wrong';
    }
}

function getContact($guid) {
    try {
        $json_contact = null;
        $url = 'http://contactsqs2.apphb.com/Service.svc/rest/contact/byguid/'.$guid;
        $response = callAPI('GET',$url, '');
        $json_contact = json_decode($response, true);
        include 'show_contact.html';
        return $json_contact;
    } catch (Exception $e) {
        header('Unauthorized', true, 401);
        echo 'Something went wrong';
    }
    return null;
}

function callAPI($method, $url, $data) {
    $curl = curl_init();
    switch ($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data) $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
}