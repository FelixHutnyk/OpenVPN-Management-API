<?php
namespace CM\InterfaceManagement;

use CM\InterfaceManagement\SocketException;

class ManagementSocket {
    private $socket = null;

    public function open($socketAddress, $timeOut = 5)  {
        $socket = \stream_socket_client($socketAddress, $errno, $errstr, $timeOut);
        if (false === $socket) {
            throw new SocketException(
                \sprintf('%s (%d)', $errstr, $errno)
            );
        }
        $this->socket = $socket;

        $this->command('log off');
    }

    public function command($command) {
        if (null === $this->socket) {
            throw new SocketException('socket not open');
        }
        self::write($this->socket, \sprintf("%s\n", $command));

        return self::read($this->socket);
    }

    public function close() {
        if (null === $this->socket) {
            throw new SocketException('socket not open');
        }
        if (false === \fclose($this->socket)) {
            throw new SocketException('unable to close the socket');
        }
    }

    private static function write($socket, $data) {
        if (false === \fwrite($socket, $data)) {
            throw new SocketException('unable to write to socket');
        }
    }

    private static function read($socket) {
        $dataBuffer = [];
        while (!\feof($socket) && !self::isEndOfResponse(\end($dataBuffer))) {
            $readData = \fgets($socket, 4096);
            if (false === $readData) {
                throw new SocketException('unable to read from socket');
            }
            $dataBuffer[] = \trim($readData);
        }
        return $dataBuffer;
    }

    private static function isEndOfResponse($lastLine) {
        $endMarkers = ['END', 'SUCCESS: ', 'ERROR: '];
        foreach ($endMarkers as $endMarker) {
            if (0 === \strpos($lastLine, $endMarker)) {
                return true;
            }
        }
        return false;
    }
}
