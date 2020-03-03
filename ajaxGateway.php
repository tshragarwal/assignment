<?php

/**
 * Task
 * 1. Create method to accept request.
 * 2. Create class for parsing response HTML
 * 3. Create class for cURL request / response.
 */
include_once 'company.php';

function getCompanyList() {
    $query = $_GET['query'];
    if($query != '' && strlen($query) >= 3) {
        //TODO create class to get list
        $company = new Company($query);
        $result = $company->companyList();

        if($result['status'] === 'success') {
            header('HTTP/1.0 200 Success');
            header('Content-Type: text/html; charset=utf-8');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            header('HTTP/1.0 801 Internal error');
            header('Content-Type: application/json');
            echo json_encode($result, true);
        }
    }
}

getCompanyList();
?>
