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
            $url = 'http://contactsqs2.apphb.com/Service.svc/rest/contacts';
            $response = callAPI('GET',$url, '');
            $allData = json_decode($response, true);
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

function getConflictsByName($allContacts) {
    $conflictsByName = [];
    $guidsAddedToConflictsByName = [];
    foreach ($allContacts as $contact) {
        foreach ($allContacts as $contactFound) {
            if ($contact["Guid"] != $contactFound["Guid"] && !in_array($contact["Guid"], $guidsAddedToConflictsByName) && strtolower($contact["GivenName"]) == strtolower($contactFound["GivenName"]) && strtolower($contact["Surname"]) == strtolower($contactFound["Surname"])) {
                array_push($guidsAddedToConflictsByName, $contactFound["Guid"]);
                if (count($conflictsByName) > 0) {
                    if (array_key_exists($contact["Guid"], $conflictsByName)) array_push($conflictsByName[$contact["Guid"]], $contactFound);
                    else $conflictsByName[$contact["Guid"]] = [$contact, $contactFound];
                } else $conflictsByName[$contact["Guid"]] = [$contact, $contactFound];
            }
        }
    }
    return $conflictsByName;
}

function getConflictsByEmail($allContacts) {
    $conflictsByEmail = [];
    $guidsAddedToConflictsByEmail = [];
    foreach ($allContacts as $contact) {
        foreach ($allContacts as $contactFound) {
            if ($contact["Guid"] != $contactFound["Guid"] && !in_array($contact["Guid"], $guidsAddedToConflictsByEmail) && strtolower($contact["Email"]) == strtolower($contactFound["Email"])) {
                array_push($guidsAddedToConflictsByEmail, $contactFound["Guid"]);
                if (count($conflictsByEmail) > 0) {
                    if (array_key_exists($contact["Guid"], $conflictsByEmail)) array_push($conflictsByEmail[$contact["Guid"]], $contactFound);
                    else $conflictsByEmail[$contact["Guid"]] = [$contact, $contactFound];
                } else $conflictsByEmail[$contact["Guid"]] = [$contact, $contactFound];
            }
        }
    }
    return $conflictsByEmail;
}

function getConflictsByPhone($allContacts) {
    $conflictsByPhone = [];
    $guidsAddedToConflictsByPhone = [];
    foreach ($allContacts as $contact) {
        foreach ($allContacts as $contactFound) {
            if ($contact["Guid"] != $contactFound["Guid"] && !in_array($contact["Guid"], $guidsAddedToConflictsByPhone) && strtolower($contact["Phone"]) == strtolower($contactFound["Phone"])) {
                array_push($guidsAddedToConflictsByPhone, $contactFound["Guid"]);
                if (count($conflictsByPhone) > 0) {
                    if (array_key_exists($contact["Guid"], $conflictsByPhone)) array_push($conflictsByPhone[$contact["Guid"]], $contactFound);
                    else $conflictsByPhone[$contact["Guid"]] = [$contact, $contactFound];
                } else $conflictsByPhone[$contact["Guid"]] = [$contact, $contactFound];
            }
        }
    }
    return $conflictsByPhone;
}

function getAllContactsAndDisplayConflicts() {
    $url = 'http://contactsqs2.apphb.com/Service.svc/rest/contacts';
    $response = callAPI('GET',$url, '');
    $allContacts = json_decode($response, true);

    $conflictsByName = getConflictsByName($allContacts);
    $conflictsByEmail = getConflictsByEmail($allContacts);
    $conflictsByPhone = getConflictsByPhone($allContacts);
    $allConflicts = ["Conflicts By Name" => $conflictsByName, "Conflicts By Email" => $conflictsByEmail, "Conflicts By Phone" => $conflictsByPhone];

    include 'show_conflicts.html';
    return $allConflicts;
}

function getContact($guid) {
    try {
        $json_contact = null;
        $url = 'http://contactsqs.apphb.com/Service.svc/rest/contact/byguid/'.$guid;
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