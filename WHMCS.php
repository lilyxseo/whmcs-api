<?php

/**
 * WHMCS API PHP Class
 * @author Kay Leacock
 */
class WHMCS {
    private $url;
    private $username;
    private $password;
    private $accesskey;

    public function __construct($url, $username, $password, $accesskey = '') {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->accesskey = $accesskey;
    }

    private function api($action, $params) {
        $params['action'] = $action;
        $params['username'] = $this->username;
        $params['password'] = md5($this->password);
        if ($this->accesskey) {
            $params['accesskey'] = $this->accesskey;
        }
        $params['responsetype'] = 'json';

        $postdata = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function authenticate($username, $password) {
        $response = $this->api("validatelogin", array("email" => $username, "password2" => $password));
        return isset($response->userid);
    }

    public function getDomains($uid = 0, $domainId = 0, $domain = '', $start = 0, $limit = 9999) {
        $params = array(
            'limitnum' => $limit,
            'limitstart' => $start
        );

        if ($uid > 0) {
            $params['clientid'] = $uid;
        }

        if ($domainId > 0) {
            $params['domainid'] = $domainId;
        }

        if ($domain) {
            $params['domain'] = $domain;
        }

        $response = $this->api("getclientsdomains", $params);
        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getDomainNameservers($domainId) {
        $params = array('domainid' => $domainId);
        $response = $this->api("domaingetnameservers", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getDomainLock($domainId) {
        $params = array('domainid' => $domainId);
        $response = $this->api("domaingetlockingstatus", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getDomainWHOIS($domainId) {
        $params = array('domainid' => $domainId);
        $response = $this->api("domaingetwhoisinfo", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getProducts($pid = 0, $gid = 0, $module = null) {
        $params = array();

        if ($pid > 0) {
            $params['pid'] = $pid;
        }

        if ($gid > 0) {
            $params['gid'] = $gid;
        }

        if ($module != null) {
            $params['module'] = $module;
        }

        $response = $this->api("getproducts", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getServices($uid = 0, $serviceId = 0, $domain = '', $productId = 0, $serviceUsername = '', $start = 0, $limit = 9999) {
        $params = array(
            'limitnum' => $limit,
            'limitstart' => $start
        );

        if ($uid > 0) {
            $params['clientid'] = $uid;
        }

        if ($serviceId > 0) {
            $params['serviceid'] = $serviceId;
        }

        if ($domain) {
            $params['domain'] = $domain;
        }

        if ($productId) {
            $params['pid'] = $productId;
        }

        if ($serviceUsername) {
            $params['username2'] = $serviceUsername;
        }

        $response = $this->api("getclientsproducts", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getTransactions($uid = 0, $invoiceId = 0, $transactionId = 0) {
        $params = array();

        if ($uid > 0) {
            $params['clientid'] = $uid;
        }

        if ($invoiceId > 0) {
            $params['invoiceid'] = $invoiceId;
        }

        if ($transactionId > 0) {
            $params['transid'] = $transactionId;
        }

        $response = $this->api("gettransactions", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getEmails($uid, $filter = '', $filterdate = '', $start = 0, $limit = 9999) {
        $params = array(
            'clientid' => $uid,
            'limitnum' => $limit,
            'limitstart' => $start
        );

        if ($filter) {
            $params['subject'] = $filter;
        }

        if ($filterdate) {
            $params['date'] = $filterdate;
        }

        $response = $this->api("getemails", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function addCredit($data) {
        $required_fields = array("clientid", "description", "amount");
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Required field $field is missing.");
            }
        }

        $response = $this->api("addcredit", $data);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getCredits($uid) {
        $response = $this->api("getcredits", array("clientid" => $uid));
        return $response;
    }

    public function updateClient($uid, $update) {
        $update['clientid'] = $uid;

        $response = $this->api("updateclient", $update);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function addClient($data) {
        $required_fields = array("firstname", "lastname", "email", "address1", "city", "state", "postcode", "country", "phonenumber", "password2");

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Required field $field is missing.");
            }
        }

        $response = $this->api("addclient", $data);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function getClient($uid = 0, $email = '') {
        $params = array();

        if ($uid > 0) {
            $params['clientid'] = $uid;
        } elseif ($email) {
            $params['email'] = $email;
        } else {
            throw new Exception("Client ID or email is required.");
        }

        $params['stats'] = true;

        $response = $this->api("getclientsdetails", $params);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function addContact($data) {
        $required_fields = array("clientid", "firstname", "lastname", "email", "address1", "city", "state", "postcode", "country", "phonenumber", "password2");

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Required field $field is missing.");
            }
        }

        $response = $this->api("addcontact", $data);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function deleteClient($uid) {
        $response = $this->api("deleteclient", array("clientid" => $uid));
        return $response;
    }

    public function addOrder($data) {
        $required_fields = array("clientid", "paymentmethod", "pid", "domain", "billingcycle");

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Required field $field is missing.");
            }
        }

        $response = $this->api("addorder", $data);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }

    public function addInvoice($data) {
        $required_fields = array("userid", "date", "duedate", "paymentmethod");

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Required field $field is missing.");
            }
        }

        $response = $this->api("createinvoice", $data);

        if ($response->result == 'error') {
            throw new Exception("WHMCS error: " . $response->message);
        }

        return $response;
    }
}

// Initialize the WHMCS API class with your credentials
$whmcs = new WHMCS('https://cs.hosterbyte.com/includes/api.php', 'apiusername', 'apipassword', 'accesskey');

// Example usage
try {
    $response = $whmcs->getClient(12345);
    print_r($response);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

?>
