<?php

// Inklusi file kelas WHMCS
require 'WHMCS.php';

// Inisialisasi kelas WHMCS
$whmcs = new WHMCS('https://cs.hosterbyte.com/includes/api.php', 'apiusername', 'apipassword', 'accesskey');

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'addDomain':
            $domainData = array(
                'domain' => $_POST['domain'],
                'registrar' => $_POST['registrar'],
                'regperiod' => $_POST['regperiod']
            );

            try {
                $response = $whmcs->registerDomain($domainData);
                echo json_encode($response);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array('error' => $e->getMessage()));
            }
            break;

        case 'addHosting':
            $hostingData = array(
                'domain' => $_POST['domain'],
                'package' => $_POST['package'],
                'billingcycle' => $_POST['billingcycle']
            );

            try {
                $response = $whmcs->addHosting($hostingData);
                echo json_encode($response);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array('error' => $e->getMessage()));
            }
            break;

        case 'getDomains':
            try {
                $domains = $whmcs->getDomains();
                $domainListHtml = '<ul class="list-group">';
                foreach ($domains['domains']['domain'] as $domain) {
                    $domainListHtml .= '<li class="list-group-item">' . $domain['domainname'] . '</li>';
                }
                $domainListHtml .= '</ul>';
                echo $domainListHtml;
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array('error' => $e->getMessage()));
            }
            break;

        case 'getHostings':
            try {
                $hostings = $whmcs->getHostings();
                $hostingListHtml = '<ul class="list-group">';
                foreach ($hostings['products']['product'] as $hosting) {
                    $hostingListHtml .= '<li class="list-group-item">' . $hosting['domain'] . ' - ' . $hosting['name'] . '</li>';
                }
                $hostingListHtml .= '</ul>';
                echo $hostingListHtml;
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(array('error' => $e->getMessage()));
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(array('error' => 'Invalid action'));
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
}

?>
