<?php
namespace CM\InterfaceManagement;

class StatusParser {

    public static function parse(array $statusData) {
        $clientList = [];

        $i = 0;
        while (0 !== strpos($statusData[$i], 'HEADER,CLIENT_LIST')) {
            ++$i;
        }
        $clientKeys = array_slice(str_getcsv($statusData[$i]), 2);
        ++$i;
        while (\strpos($statusData[$i], 'CLIENT_LIST') === 0) {
            $clientValues = \str_getcsv($statusData[$i]);
            \array_shift($clientValues);
            $clientInfo = \array_combine($clientKeys, $clientValues);
            $clientList[] = [
                'username' => $clientInfo['Common Name'],
                'real_address' => $clientInfo['Real Address'],
                'virtual_address' => $clientInfo['Virtual Address'],
                'bytes_received' => $clientInfo['Bytes Received'],
                'bytes_sent' => $clientInfo['Bytes Sent'],
                'connection_since' => $clientInfo['Connected Since (time_t)'],
            ];
            ++$i;
        }

        return $clientList;
    }
}
