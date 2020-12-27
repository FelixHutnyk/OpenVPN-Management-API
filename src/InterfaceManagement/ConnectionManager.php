<?php

use SocketException;

class ConnectionManager {

   private $socketAddress;

   private $managementSocket;

    public function __construct($socketAddress) {
        $this->socketAddress = $socketAddress;
        $managementSocket = new ManagementSocket();
        $this->managementSocket = $managementSocket;
    }


    public function connections() {
        $connectionList = [];
        try {
            $this->managementSocket->open($socketAddress);
            $connectionList = array_merge($connectionList, StatusParser::parse($this->managementSocket->command('status 2')));
            $this->managementSocket->close();
        } catch (ManagementSocketException $e) {
            // $this->logger->error(sprintf('error with socket "%s": "%s"', $socketAddress, $e->getMessage()));
        }

        return $connectionList;
    }

    public function disconnect($commonName) {
        $disconnectCount = 0;
        foreach ($this->socketAddressList as $socketAddress) {
            try {
                $this->managementSocket->open($socketAddress);
                $result = $this->managementSocket->command(sprintf('kill %s', $commonName));
                if (0 === strpos($result[0], 'SUCCESS: ')) {
                    ++$disconnectCount;
                }
                $this->managementSocket->close();
            } catch (ManagementSocketException $e) {
                // $this->logger->error(sprintf('error with socket "%s", message: "%s"',$socketAddress,$e->getMessage()));
            }
        }

        return $disconnectCount;
    }
    ?>
