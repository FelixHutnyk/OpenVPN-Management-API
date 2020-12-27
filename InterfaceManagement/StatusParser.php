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
        while (0 === strpos($statusData[$i], 'CLIENT_LIST')) {
            $clientValues = str_getcsv($statusData[$i]);
            array_shift($clientValues);
            $clientInfo = array_combine($clientKeys, $clientValues);
            $clientList[] = [
                'common_name' => $clientInfo['Common Name'],
                'virtual_address' => [
                    $clientInfo['Virtual Address'],
                    $clientInfo['Virtual IPv6 Address'],
                ],
            ];
            ++$i;
        }

        return $clientList;
    }
}
