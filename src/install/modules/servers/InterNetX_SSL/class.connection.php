<?php

/* * ********************************************************************
 * InterNetX registrar module. Product developed. (2014-02-21)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

if (!class_exists("InterNetX_SSL_API")) {

    class InterNetX_SSL_API {

        private static $serverUrl;
        private $errors = array();
        private static $username;
        private static $password;
        private static $context;
        private static $testMode;
        private static $debug;

        /**
         * FUNCTION __construct
         * Create object api
         * @param $server_url
         * @param string $username
         * @param string $password
         * @param string $debug
         * @param string $testMode
         */
        public function __construct($serverUrl, $username, $password, $context,
                                     $debug = true) {

                self::$testMode   = false;
                self::$serverUrl  = 'https://'.str_replace('https://','',$serverUrl);
                self::$username   = $username;
                self::$password   = htmlspecialchars_decode($password);
                self::$context    = $context;
                self::$debug      = (bool) $debug;
        }

        /**
         * FUNCTION addError
         * Add new error
         * @param string $error
         */
        protected function addError($error) {
            if (!in_array($error, $this->errors))
                $this->errors[] = $error;
        }

        /**
         * FUNCTION hasError
         * Is error?
         * @return boolean
         */
        public function hasError() {
            return empty($this->errors) ? false : true;
        }

        /**
         * FUNCTION getLastError
         * Return Last Error And Unset It
         * @return boolean
         */
        public function getLastError() {
            if (!$this->errors) {
                return false;
            }
            $count = count($this->errors) - 1;
            $error = $this->errors[$count];
            unset($this->errors[$count]);
            return $error;
        }

        public function cleanErrors()
        {
            unset($this->errors);
        }

        /**
         * FUNCTION getError
         * Return all errors
         * @return string $ret
         */
        public function getErrors() {

            $ret = null;
            foreach ($this->errors as $value) {
                $ret.=$value . " ";
            }
            if ($ret == null)
                return false;
            else {
                $this->errors = array();
                return $ret;
            }
        }

        /**
         * FUNCTION parseXML
         * @param string $xml
         * @return boolean|object
         */
        private function parseXML($xml) {

            $search = array('contact:', "domain:", "no-ext-", "host:", "domain-ext:",'<![CDATA[',']]>');
            $replace = array('contact_', "domain_", "no_ext_", "host_", "domain-ext_",'','');
            $xml = str_replace($search, $replace, $xml);

            if (!$xml) {
                $this->addError("Parsing XML failed");
                return false;
            }
            $a = simplexml_load_string($xml);

            if ($a === FALSE) {
                $this->addError("Parsing XML failed");
                return false;
            } else {
                return $a;
            }
        }

        private function call($request) {
            //$request = str_replace('&amp;','&',$request);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$serverUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $data = curl_exec($ch);
            if ($data === false) {
                $err = ucwords(curl_error($ch)) ? ucwords(curl_error($ch)) : "Unable connect to registrar";
                $this->addError("CURL: ".curl_error($ch));
                curl_close($ch);
                return false;
            }
            curl_close($ch);
            return $data;
        }

        /**
         * FUNCTION createRequest
         * Create xml
         * @param array $params
         * @return string
         */
        private function createRequest($params) {
            $xml = new DomDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            $request = $xml->createElement('request');
            $xml->appendChild($request);
            $auth = $xml->createElement('auth');
            $request->appendChild($auth);
            $user = $xml->createElement('user', self::$username);
            $auth->appendChild($user);
            $pass = htmlspecialchars(self::$password);
            $password = $xml->createElement('password', $pass);
            $auth->appendChild($password);
            $context = $xml->createElement('context', self::$context);
            $auth->appendChild($context);
            $language = $xml->createElement('language', "en");
            $request->appendChild($language);
            $task = $xml->createElement('task');
            $request->appendChild($task);
            if (is_array($params)) {
                $this->prepareArray($xml, $task, $params);
            }else if(is_object($params)){
                $this->prepareObject($xml, $task, $params);
            }

            return $xml->saveXML();
        }

        /**
         * FUNCTION prepareArray
         * Helper function modify xml
         * @param object $xmldoc
         * @param object $node
         * @param $array
         */
        private function prepareArray(&$xmldoc, &$node, $array) {
            foreach ($array as $k => $value) {
                if (is_array($value)) {
                    $nd = $xmldoc->createElement($k);
                    $this->prepareArray($xmldoc, $nd, $value);
                } else {
                    $nd = $xmldoc->createElement($k, $value);
                    $nd->setAttribute($key, $v);
                    $node->appendChild($nd);
                }
            }
        }
        /**
         * FUNCTION prepareObject
         * Helper function modify xml
         * @param object $xmldoc
         * @param object $node
         * @param object
         */
        private function prepareObject(&$xmldoc, &$node, $object) {

            foreach ($object as $k => $value) {

                if($k == 'san')
                {
                    foreach($value as $v)
                    {
                        $nd = $xmldoc->createElement((string)$k,$v);
                        $node->appendChild($nd);
                    }
                }elseif (is_object($value)) {

                    $nd = $xmldoc->createElement((string)$k);
                    $this->prepareObject($xmldoc, $nd, $value);
                    $node->appendChild($nd);
                }
                elseif(is_array($value)){
                      foreach($value as $v){

                          $nd2 = $xmldoc->createElement((string) $k);
                          $node->appendChild($nd2);
                          foreach($v as $childKey => $childVal){
                              $child = $xmldoc->createElement((string) $childKey, (string) $childVal);
                              $nd2->appendChild($child);
                          }
                      }
                }else {
                    $nd = $xmldoc->createElement((string) $k, (string) $value);
                    $node->appendChild($nd);
                }
            }
        }


        private function processRequest($request,$action) {

            $response = $this->call($request);
            if (self::$debug && function_exists('logModuleCall')) {
                logModuleCall(
                        "InterNetX", $action, print_r($request, true), '', print_r($response, true), array(self::$username, str_replace('&','&amp;',self::$password))
                );

//                logModuleCall(
//                        "InterNetX", $action, print_r($request, true), '', print_r($response, true)
//                );

            }

            if($response===false)
                return;

            $result = $this->parseXML($response);

            if(isset($result->result->msg->type) && $result->result->msg->type=="error"){
                foreach($result->result->msg as $msg){
                   $err = (string) $msg->code." ".$msg->text;
                   $this->addError($err);
                }
                $this->addError((string)$result->result->msg->code." ".$result->result->msg->text);
            }

            if(isset($result->result->status->type) && $result->result->status->type=="error"){
                $this->addError((string)$result->result->status->code." ".$result->result->status->text);
            }

            if($this->hasError())
                return false;

            return $result;

        }


        public function test($params)
        {
            $task = new stdClass();
            $task->code = 400104;
            $task->certificate->id   = 47;
            $task->certificate->authentication->method = 'DNS';
            $task->certificate->authentication->provisioning = 1;

            $xml    = $this->createRequest($task);
            $result = $this->processRequest($xml, "create Cert");

            return $result;
        }


        public function createCert($params)
        {
            $lifetime                           = empty($params['configoptions']['lifetime']) ? $params['configoption6'] : $params['configoptions']['lifetime'];
            $task                               = new stdClass();
            $task->code                         = 400101;
            $task->certificate->admin->id       = $params['configdata']['admin_id'];
            $task->certificate->technical->id   = $params['configdata']['tech_id'];
            $task->certificate->name            = $params['domain'];
            $task->certificate->approver_email  = $params['configdata']['approveremail'];
            $task->certificate->product         = $params['configoption5'];
            $task->certificate->lifetime        = $lifetime;
            $task->certificate->expire          = date('Y-m-d H:i:s',strtotime('+'.$lifetime.' month',time()));
            $task->certificate->csr             = $params['csr'];

            if($params['configoption5'] == 'THAWTECODESIGNING')
            {
                $task->certificate->type        = empty($params['configdata']['servertype']) ? $params['configdata']['software'] : $params['configdata']['servertype'];
            } else
            {
                $task->certificate->software    = empty($params['configdata']['servertype']) ? $params['configdata']['software'] : $params['configdata']['servertype'];
            }

            if(isset($params['configdata']['fields']['Hosts']))
            {
                $data = explode("\n", $params['configdata']['fields']['Hosts']);
                if(count($data) > 0)
                {
                   $task->certificate->san = $data;
                }
            }
            if($params['configoption5'] == 'BASIC_SSL')
            {
                $task->certificate->authentication->method = 'DNS';
                $task->certificate->authentication->provisioning = 1;
            }
            $xml    = $this->createRequest($task);
            $result = $this->processRequest($xml, "create Cert");

            return $result;

        }

        public function createCertNew($params)
        {
            $lifetime                                  = empty($params['configoptions']['lifetime']) ? $params['configoption6'] : $params['configoptions']['lifetime'];
            $task                                      = new stdClass();
            $task->code                                = 400101;
            $task->certificate->certificate_type       = 'MAIL';
            $task->certificate->product                = $params['configoption5'];
            $task->certificate->lifetime               = $lifetime;
            $task->certificate->name                   = $params['configdata']['approveremail'];
            $task->certificate->approver_email         = $params['configdata']['approveremail'];
            $task->certificate->password               = $params['configdata']['fields']['enduser_password'];
            $task->certificate->firstname              = $params['firstname'];
            $task->certificate->lastname               = $params['lastname'];
            $task->certificate->organization_unit_name = $params['jobtitle'];
            $task->certificate->has_csr                = 'false';
            $task->certificate->admin->id              = $params['configdata']['admin_id'];

            $xml    = $this->createRequest($task);
            $result = $this->processRequest($xml, "create Cert New");

            return $result;

        }

        public function revokeCert($params)
        {
            $task                               = new stdClass();
            $task->code                         = 400103;
            $task->certificate->id              = $params['remoteid'];
            if($params['configoption5'] == 'BASIC_SSL')
            {
                $task->certificate->authentication->method = 'DNS';
                $task->certificate->authentication->provisioning = 1;
            }
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "revoke Cert");
            return $result;
        }

        public function reissue($params)
        {
            $task                               = new stdClass();
            $task->code                         = 400102;
            $task->certificate->id              = $params['remoteid'];
            $task->certificate->csr             = $params['csr'];
            $task->certificate->name            = $params['domain'];
            $task->certificate->approver_email  = $params['approver_email'];
            $task->product                      = $params['ProductName'];
            if($params['ProductName'] == 'BASIC_SSL')
            {
                $task->certificate->authentication->method = 'DNS';
                $task->certificate->authentication->provisioning = 1;
            }
            //$task->certificate->type            = $params['servertype'];
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "reissue Cert");
        }

        public function renewCert($params)
        {
            $lifetime                           = empty($params['configoptions']['lifetime']) ? $params['configoption6'] : $params['configoptions']['lifetime'];
            $task                               = new stdClass();
            $task->code                         = 400106;
            $task->certificate->id              = (int)$params['original']['id'];
            $task->certificate->admin->id       = (int)$params['original']['admin']->id;
            $task->certificate->technical->id   = (int)$params['original']['technical']->id;
            $task->certificate->name            = $params['domain'];
            $task->certificate->approver_email  = (string)$params['original']['approver_email'];
            $task->certificate->product         = $params['configoption5'];
            $task->certificate->lifetime        = $lifetime;
            $task->certificate->expire          = date('Y-m-d H:i:s',strtotime('+'.$lifetime.' month',time()));
            $task->certificate->software        = (string)$params['original']['software'];
            $task->certificate->csr             = (string)$params['configdata']['csr'];
            if($params['configoption5'] == 'BASIC_SSL')
            {
                $task->certificate->authentication->method = 'DNS';
                $task->certificate->authentication->provisioning = 1;
            }
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "renew Cert");
            return $result;
        }

        public function renewCertNew($params)
        {
            $lifetime                                  = empty($params['configoptions']['lifetime']) ? $params['configoption6'] : $params['configoptions']['lifetime'];
            $task                                      = new stdClass();
            $task->code                                = 400101;
            $task->certificate->id                     = (int)$params['original']['id'];
            $task->certificate->certificate_type       = 'MAIL';
            $task->certificate->product                = $params['configoption5'];
            $task->certificate->lifetime               = $lifetime;
            $task->certificate->name                   = $params['configdata']['approveremail'];
            $task->certificate->approver_email         = $params['configdata']['approveremail'];
            $task->certificate->password               = $params['configdata']['fields']['enduser_password'];
            $task->certificate->firstname              = $params['configdata']['firstname'];
            $task->certificate->lastname               = $params['configdata']['lastname'];
            $task->certificate->organization_unit_name = $params['configdata']['jobtitle'];
            $task->certificate->has_csr                = 'false';
            $task->certificate->admin->id              = (int)$params['original']['admin']->id;

            $xml                                       = $this->createRequest($task);
            $result                                    = $this->processRequest($xml, "renew Cert New");
            return $result;
        }

        public function searchCert($params)
        {
            $task                               = new stdClass();
            $task->code                         = 400105;
            $task->view->limit                  = 1;
            $task->view->offset                 = 0;
            $task->where->key                   = 'name';
            $task->where->operator              = 'eq';
            $task->where->value                 = $params['domain'];
            if($params['configoption5'] == 'BASIC_SSL')
            {
                $task->certificate->authentication->method = 'DNS';
                $task->certificate->authentication->provisioning = 1;
            }
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "search Cert");
            if((int)$result->result->data->certificate->id > 0)
            {
                return $result->result->data->certificate;
            }
        }

        public function searchJob($params)
        {
            $task                               = new stdClass();
            $task->code                         = 400115;
            $task->view->limit                  = 100;
            $task->view->offset                 = 0;
            $task->where->key                   = 'object';
            $task->where->operator              = 'like';
            $task->where->value                 = $params['domain'];

            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "search job Cert");

            if((int)$result->result->data->certificate->id > 0)
            {
                return $result->result->data->certificate;
            }
        }

        public function searchSingleJob($id, $res)
        {
            $task                               = new stdClass();
            $task->code                         = 400114;
            $task->certificate_job->job->id     = $id;
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "search single job Cert");

            if($res)
                return $result;

            if((int)$result->result->data->certificate_job->certificate->id > 0)
            {
                return (int)$result->result->data->certificate_job->certificate->id;
            }
        }

        public function searchSingleJob2($id, $res)
        {
            $task                               = new stdClass();
            $task->code                         = 400134;
            $task->certificate_job->job->id     = $id;
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "search single job2 Cert");

            if($res)
                return $result;

            if((int)$result->result->data->certificate_job->certificate->id > 0)
            {
                return (int)$result->result->data->certificate_job->certificate->id;
            }
        }

        public function getDetails($id)
        {
            $task                               = new stdClass();
            $task->code                         = 400104;
            $task->certificate->id              = $id;
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "get details SSL");
            return $result;
        }



        public function resendEmail($params)
        {
            $task                               = new stdClass();
            $task->code                         = 400117;
            $task->certificate_job->id          = 9020837;
            $task->certificate_job->type        = 'RESENT';
            $task->certificate_job->vertex      = 87;
            $xml                                = $this->createRequest($task);
            $result                             = $this->processRequest($xml, "resend approve email");
            return $result;
        }


        public function contactCreate($contact,$type){

            $task                           = new stdClass();
            $task->code                     = 400201;
            $task->contact->first           = $contact[$type.'FirstName'];
            $task->contact->last            = $contact[$type.'LastName'];
            $task->contact->title           = $contact[$type.'Title'];
            $task->contact->organization    = $contact[$type.'Name'];
            $task->contact->address         = $contact[$type.'Address1'].' '.$contact[$type.'Address2'];
            $task->contact->postal_code     = $contact[$type.'PostalCode'];
            $task->contact->city            = $contact[$type.'City'];
            $task->contact->state           = $contact[$type.'State'];
            $task->contact->country         = $contact[$type.'Country'];
            $task->contact->phone           = $contact[$type.'Phone'];
            $task->contact->fax             = $contact[$type.'Fax'];
            $task->contact->email           = $contact[$type.'Email'];
            $task->force_contact_create     = 1;
            //$task->reply_to                 = $contact['']

            $xml = $this->createRequest($task);
            $result = $this->processRequest($xml, "contact create");

            return $result;
        }



        public function contactUpdate($contact,$type){
            $task                           = new stdClass();
            $task->code                     = 400202;
            $task->contact->id              = $contact[$type.'id'];
            $task->contact->first           = $contact[$type.'FirstName'];
            $task->contact->last            = $contact[$type.'LastName'];
            $task->contact->title           = $contact[$type.'Title'];
            $task->contact->organization    = $contact[$type.'Name'];
            $task->contact->address         = $contact[$type.'Address1'].' '.$contact[$type.'Address2'];
            $task->contact->postal_code     = $contact[$type.'PostalCode'];
            $task->contact->city            = $contact[$type.'City'];
            $task->contact->state           = $contact[$type.'State'];
            $task->contact->country         = $contact[$type.'Country'];
            $task->contact->phone           = $contact[$type.'Phone'];
            $task->contact->fax             = $contact[$type.'Fax'];
            $task->contact->email           = $contact[$type.'Email'];
            $xml = $this->createRequest($task);
            return $this->processRequest($xml, "contact update");

        }


    }

}
