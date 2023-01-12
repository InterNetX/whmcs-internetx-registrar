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

if (!class_exists("API_Connect")) {

    /**
     * Support for InterNetX API
     */
    class API_Connect
    {

        private static $serverUrl;
        private $errors = array();
        private static $username;
        private static $password;
        private static $context;
        private static $testMode;
        private static $debug;
        private static $reply_to;

        /**
         * FUNCTION __construct
         * Create object api
         * @param $server_url
         * @param string $username
         * @param string $password
         * @param string $debug
         * @param string $testMode
         */
        public function __construct($serverUrl = 'https://gateway.autodns.com', $username, $password, $context, $sanboxUsername, $sanboxPassword, $sanboxContext, $debug = false, $testMode = false)
        {

            if ($testMode) {
                self::$testMode  = true;
                self::$serverUrl = "https://demo.autodns.com/gateway/";
                self::$username  = $sanboxUsername;
                self::$password  = $sanboxPassword;
                self::$context   = $sanboxContext;
            } else {
                self::$testMode  = false;
                self::$serverUrl = $serverUrl;
                self::$username  = $username;
                self::$password  = $password;
                self::$context   = $context;
            }
            self::$debug = (boolean) $debug;
        }

        /**
         * FUNCTION addError
         * Add new error
         * @param string $error
         */
        protected function addError($error)
        {
            if (!in_array($error, $this->errors)) $this->errors[] = $error;
        }

        /**
         * FUNCTION hasError
         * Is error?
         * @return boolean
         */
        public function hasError()
        {
            return empty($this->errors) ? false : true;
        }

        /**
         * FUNCTION getLastError
         * Return Last Error And Unset It
         * @return boolean
         */
        public function getLastError()
        {
            if (!$this->errors) {
                return false;
            }
            $count = count($this->errors) - 1;
            $error = $this->errors[$count];
            unset($this->errors[$count]);
            return $error;
        }

        /**
         * FUNCTION getError
         * Return all errors
         * @return string $ret
         */
        public function getErrors()
        {

            $ret = null;
            foreach ($this->errors as $value) {
                $ret.=$value . " ";
            }
            if ($ret == null) return false;
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
        private function parseXML($xml)
        {

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

        /**
         * FUNCTION call
         * Call to  API
         * @param string $request
         * @return boolean|string
         */
        private function call($request)
        {

            $ch   = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::$serverUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $data = curl_exec($ch);

            if ($data === false) {
                $err = ucwords(curl_error($ch)) ? ucwords(curl_error($ch)) : "Unable connect to registrar";
                $this->addError("CURL: " . curl_error($ch));
                curl_close($ch);
                return false;
            }
            curl_close($ch);
            return $data;
        }

        /**
         * FUNCTION createRequest
         * Create  xml request
         * @param array $params
         * @return string
         */
        private function createRequest($params)
        {

            $xml               = new DomDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            $request           = $xml->createElement('request');
            $xml->appendChild($request);
            $auth              = $xml->createElement('auth');
            $request->appendChild($auth);
            $user              = $xml->createElement('user', self::$username);
            $auth->appendChild($user);
            $password          = $xml->createElement('password', self::$password);
            $auth->appendChild($password);
            $context           = $xml->createElement('context', self::$context);
            $auth->appendChild($context);
            $language          = $xml->createElement('language', "en");
            $request->appendChild($language);
            $userAgent         = $xml->createElement('user_agent', "WHMCS7 Plugin");
            $request->appendChild($userAgent);
            $task              = $xml->createElement('task');
            $request->appendChild($task);

            if (is_object($params)) {
                $this->prepareObject($xml, $task, $params);
            }
            return $xml->saveXML();
        }

        /**
         * FUNCTION prepareObject
         * Helper function modify xml
         * @param object $xmldoc
         * @param object $node
         * @param object $object
         */
        private function prepareObject(&$xmldoc, &$node, $object)
        {

            foreach ($object as $k => $value) {

                if (is_object($value)) {
                    $nd = $xmldoc->createElement((string) $k);
                    $this->prepareObject($xmldoc, $nd, $value);
                    $node->appendChild($nd);
                } elseif (is_array($value)) {
                    foreach ($value as $v) {
                        $nd2 = $xmldoc->createElement((string) $k);
                        $node->appendChild($nd2);
                        foreach ($v as $childKey => $childVal) {
                            $child = $xmldoc->createElement((string) $childKey, (string) $childVal);
                            $nd2->appendChild($child);
                        }
                    }
                } else {
                    $nd = $xmldoc->createElement((string) $k, (string) $value);
                    $node->appendChild($nd);
                }
            }
        }

        /**
         * FUNCTION processRequest
         * Process request
         * @param string $request
         * @param string $action
         * @return boolean|object
         */
        private function processRequest($request, $action)
        {

            $response = $this->call($request);
            if (self::$debug && function_exists('logModuleCall')) {
                logModuleCall(
                    "InterNetXAddonModule", $action, print_r($request, true), '', print_r($response, true), array(
                    self::$username, self::$password)
                );
            }

            if ($response === false) {
                return;
            }

            if ($action == 'checkDomainStatus')
            {
                return $this->parseXML($response);
            }

            $result = $this->parseXML($response);

            return $result;
        }


        /**
         * FUNCTION checkPremiumAvability
         * Checking if domain is avalible for registration of transfer an geting pricing class of premium domains
         * @param string $domain
         * @param string $type checkavability
         * @return object|boolean
         */
        public function checkPremiumAvability($domain, $action)
        {

            $task                        = new stdClass();
            $task->code                  = "0164";
            $task->domain_premium->name  = $domain;

            $xml = $this->createRequest($task);
            return $this->processRequest($xml, $action);
        }

        public function setReplyTo($replyTo)
        {
            self::$reply_to = $replyTo;
        }
    }
}
