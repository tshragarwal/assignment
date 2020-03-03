<?php

/**
 * Task.
 * 1. Create class basic structure
 * 2. Expose public function which return company list
 * 3. Initiate curl request to get HTML.
 * 4. Parse response HTML and generate desire list.
 */

require __DIR__ . '/vendor/autoload.php';

use \Curl\Curl;

 class Company {

    //As we are only looking for cez republic
    private $postData = [
        'search' => 1,
        'countryCode' => 'cz',
        'countryName' => 'Czech Republic'
    ];

    private $url = 'https://e-creditreform.cz/search';

    public function __construct ($queryString) {
        $this->postData['query'] = $queryString;
    }

    public function companyList() {
        //TODO initiate cURL request
        $curl = new Curl();
        $curl->post( $this->url, $this->postData);

        if ($curl->error) {
            return [
                'status' => 'Fail',
                'errorCode' => $curl->errorCode,
                'errorMessage' => $curl->errorMessage
            ];
        } else {
            return [
                'status' => 'success',
                'data' => $this->parseHTML($curl->response)
            ];
        }
        
    }

    private function parseHTML($response) {
        $companyList = [];
        $dom = new DOMDocument('11.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML($response);

        $elements = $dom->getElementById('content')->getElementsByTagName('a');
        foreach($elements as $company) {
            $companyList[$company->getAttribute('href')] = $this->formatCompanyDetails($company);
        }
        return $companyList;
    }

    private function formatCompanyDetails($companyDom) {
        $address = $companyDom->getElementsByTagName('div');
        $formatedAddress = [];
        $i = 0;
        foreach($address as $line) {
            //For title
            if($i === 0) {
                $tmp = explode(',', $line->textContent);
                $formatedAddress['id'] = trim($tmp[0]);
                $formatedAddress['name'] = trim($tmp[1]);
            } else {
                //For text
                $tmp = explode(',', $line->textContent);

                //For streetname sepration
                $streetDetails = explode(' ', $tmp[0]);
                $formatedAddress['streetName'] = trim($streetDetails[0]);

                $cpDetails = explode('/', $streetDetails[1]);
                $formatedAddress['cp'] = $cpDetails[0];
                $formatedAddress['co'] = isset($cpDetails[1]) ? $cpDetails[1] : '';
                
                if(count($tmp) === 4) {
                    //For city and district
                    $formatedAddress = array_merge( $formatedAddress, $this->getCityDetails($tmp[1]));
                    $formatedAddress = array_merge($formatedAddress, $this->getCountryDetails($tmp[3]));
                } else if(count($tmp) === 3) {
                    //For city and district
                    $formatedAddress = array_merge( $formatedAddress, $this->getCityDetails($tmp[1]));
                    $formatedAddress = array_merge($formatedAddress, $this->getCountryDetails($tmp[2]));
                } else if (count($tmp) === 2) {
                    $formatedAddress = array_merge($formatedAddress, $this->getCountryDetails($tmp[1]));
                }
            }
            $i++;
        }
        return $formatedAddress;
    }

    private function getCityDetails($city) {
        $cityDetails = explode('-', $city);
        return [
            'city' => trim($cityDetails[0]),
            'district' => isset($cityDetails[1]) ? trim($cityDetails[1]) : ''
        ];
    }

    private function getCountryDetails($country) {
        preg_match_all('/\d+/', $country, $matches);
        if(count($matches[0]) > 0) {
            return [
                'country' => trim(substr($country, 0, strlen($country) - 5)),
                'postalCode' => $matches[0][0]
            ];
        } else {
            return [
                'country' => trim($country),
                'postalCode' => ''
            ];
        }
    }

 }

?>
