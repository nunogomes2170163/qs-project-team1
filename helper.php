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

function flattenArray($arrayToFlatten = []) {
    $flatArray = [];
    foreach (array_values($arrayToFlatten) as $key => $val)
        foreach ($val as $k => $v)
            array_push($flatArray, $v);
    return $flatArray;
}

function searchForGuid($id, $array) {
    foreach ($array as $key => $val)
        if ($val['Guid'] === $id) return $key;
    return null;
}

function getKeysToRemove($flatArray, $allContacts) {
    $keysToDelete = [];
    foreach ($flatArray as $conflict) {
        $index = searchForGuid($conflict["Guid"], $allContacts);
        array_push($keysToDelete, $index);
    }
    return $keysToDelete;
}

function removeConflictsFromContacts($allContacts, $nameConflicts, $emailConflicts, $phoneConflicts) {
    $nameConflictsFlat = flattenArray($nameConflicts);
    $emailConflictsFlat = flattenArray($emailConflicts);
    $phoneConflictsFlat = flattenArray($phoneConflicts);
    $keysToRemoveFromNameConflicts = getKeysToRemove($nameConflictsFlat, $allContacts);
    $keysToRemoveFromEmailConflicts = getKeysToRemove($emailConflictsFlat, $allContacts);
    $keysToRemoveFromPhoneConflicts = getKeysToRemove($phoneConflictsFlat, $allContacts);
    $keysToRemove = array_merge($keysToRemoveFromNameConflicts, $keysToRemoveFromEmailConflicts, $keysToRemoveFromPhoneConflicts);
    return array_diff_key($allContacts, array_flip($keysToRemove));
}

function getAllContactsAndDisplayConflicts() {
    // resetExportData();
    if (!$_SESSION["contactsListToExport"] && !$_SESSION["conflictsByName"] && !$_SESSION["conflictsByEmail"] && !$_SESSION["conflictsByPhone"]) {
        $url = 'http://contactsqs2.apphb.com/Service.svc/rest/contacts';
        $response = callAPI('GET',$url, '');
        $allContacts = json_decode($response, true);
        $conflictsByName = getConflictsByName($allContacts);
        $conflictsByEmail = getConflictsByEmail($allContacts);
        $conflictsByPhone = getConflictsByPhone($allContacts);
        $allContactsWithoutConflicts = removeConflictsFromContacts($allContacts, $conflictsByName, $conflictsByEmail, $conflictsByPhone);
        setExportData($allContacts, $allContactsWithoutConflicts, $conflictsByName, $conflictsByEmail, $conflictsByPhone);
        $allConflictGroups = ["Conflicts By Name" => $conflictsByName, "Conflicts By Email" => $conflictsByEmail, "Conflicts By Phone" => $conflictsByPhone];
    } else {
        $allConflictGroups = ["Conflicts By Name" => $_SESSION["conflictsByName"], "Conflicts By Email" => $_SESSION["conflictsByEmail"], "Conflicts By Phone" => $_SESSION["conflictsByPhone"]];
    }

    include 'show_conflicts.html';
    return $allConflictGroups;
}

function getEnvolvedContactsAndDisplayConflict() {
    $guids = explode("|", $_GET["guids"]);
    $envolvedContacts = [];
    foreach ($guids as $guid) {
        foreach ($_SESSION["allContacts"] as $key => $val) {
            if ($val["Guid"] == $guid) {
                array_push($envolvedContacts, $val);
            }
        }
    }
    include 'show_single_conflict.html';
    $_SESSION["currentConflict"] = $envolvedContacts;
    return $envolvedContacts;
}

function setExportData($allContacts, $allContactsWithoutConflicts, $conflictsByName, $conflictsByEmail, $conflictsByPhone) {
    $_SESSION["allContacts"] = $allContacts;
    $_SESSION["contactsListToExport"] = $allContactsWithoutConflicts;
    $_SESSION["conflictsByName"] = $conflictsByName;
    $_SESSION["conflictsByEmail"] = $conflictsByEmail;
    $_SESSION["conflictsByPhone"] = $conflictsByPhone;
}

function resetExportData() {
    unset($_SESSION["allContacts"]);
    unset($_SESSION["contactsListToExport"]);
    unset($_SESSION["conflictsByName"]);
    unset($_SESSION["conflictsByEmail"]);
    unset($_SESSION["conflictsByPhone"]);
    header('Location: get_contacts.php');
}

function resolveConflict($resolvedContact) {
    $guidsToDelete = explode("|", $resolvedContact["Guid"]);
    foreach ($guidsToDelete as $guid) {
        foreach ($_SESSION["conflictsByName"] as $key => $cByName) {
            if ($key == $guid) unset($_SESSION["conflictsByName"][$key]);
        }
        foreach ($_SESSION["conflictsByEmail"] as $key => $cByEmail) {
            if ($key == $guid) unset($_SESSION["conflictsByEmail"][$key]);
        }
        foreach ($_SESSION["conflictsByPhone"] as $key => $cByPhone) {
            if ($key == $guid) unset($_SESSION["conflictsByPhone"][$key]);
        }
    }
    unset($resolvedContact["Guid"]);
    unset($resolvedContact["Source"]);
    array_push($_SESSION["contactsListToExport"], $resolvedContact);
    echo json_encode(["status" => "200"]);
}

function dismissContactConflicts($guids) {
    $resolvedContacts = [];
    $guidsToDelete = explode("|", $guids);
    foreach ($guidsToDelete as $guid) {
        foreach ($_SESSION["conflictsByName"] as $key => $cByName) {
            if ($key == $guid) {
                foreach ($cByName as $k) {
                    if (!in_array($k, $resolvedContacts)) {
                        array_push($resolvedContacts, $k);
                    }
                }
                unset($_SESSION["conflictsByName"][$key]);
            }
        }
        foreach ($_SESSION["conflictsByEmail"] as $key => $cByEmail) {
            if ($key == $guid) {
                foreach ($cByEmail as $k) {
                    if (!in_array($k, $resolvedContacts)) {
                        array_push($resolvedContacts, $k);
                    }
                }
                unset($_SESSION["conflictsByEmail"][$key]);
            }
        }
        foreach ($_SESSION["conflictsByPhone"] as $key => $cByPhone) {
            if ($key == $guid) {
                foreach ($cByPhone as $k) {
                    if (!in_array($k, $resolvedContacts)) {
                        array_push($resolvedContacts, $k);
                    }
                }
                unset($_SESSION["conflictsByPhone"][$key]);
            }
        }
    }
    $merged = array_merge($_SESSION["contactsListToExport"], $resolvedContacts);
    $_SESSION["contactsListToExport"] = $merged;
}

function generateCsv($data, $delimiter = ',', $enclosure = '"') {
    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, ["Birthday", "City", "Company", "Email", "First Name", "Occupation", "Phone", "PhotoUrl", "Address", "Last Name"], $delimiter, $enclosure);
    foreach ($data as $line) {
        fputcsv($handle, $line, $delimiter, $enclosure);
    }
    rewind($handle);
    $contents = '';
    while (!feof($handle)) {
        $contents .= fread($handle, 8192);
    }
    fclose($handle);
    return $contents;
}

function exportCsv() {
    $contactsReadyToExport = [];
    foreach ($_SESSION["contactsListToExport"] as $contact) {
        unset($contact["Guid"]);
        unset($contact["Source"]);
        array_push($contactsReadyToExport, $contact);
    }
    $csv = generateCsv($contactsReadyToExport);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contacts.csv"');
    echo $csv;
    header('Location: resolve_conflicts.php');
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