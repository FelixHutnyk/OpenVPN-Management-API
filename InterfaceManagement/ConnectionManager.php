<?php
namespace CM\InterfaceManagement;

use CM\InterfaceManagement\SocketException;

class ConnectionManager {

    private $socketAddress;

    private $managementSocket;

    public function __construct($socketAddress) {
        $this->socketAddress = $socketAddress;
        $this->managementSocket = new ManagementSocket();
    }

    public function connections() {
        $connectionList = [];
        try {
            $this->managementSocket->open($this->socketAddress);
            $connectionList = StatusParser::parse($this->managementSocket->command('status 2'));
            $this->managementSocket->close();
        } catch (SocketException $e) {
            die(sprintf('error with socket "%s": "%s"',$this->socketAddress, $e->getMessage()));
        }

        return $connectionList;
    }

    public function disconnect($commonName) {
        $status = false;
        try {
            $this->managementSocket->open($this->socketAddress);
            $result = $this->managementSocket->command(sprintf('kill %s', $commonName));
            if (strpos($result[0], 'SUCCESS: ') === 1) {
                $status = true;
            }
            $this->managementSocket->close();
        } catch (SocketException $e) {
            die(sprintf('error with socket "%s": "%s"', $this->socketAddress, $e->getMessage()));
        }

        return $status;
    }
}
