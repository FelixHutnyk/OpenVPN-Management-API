<?php
function ok($response, $data="OK") {
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(200, "OK")
    ->withJson($data);
    return $response;
}

function error($response, $err="Conflict") {
    $data['status'] = $err;
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(409, "Conflict")
    ->withJson($data);
    return $response;
}

function unauthorized($response) {
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(401, "Unauthorized");
    return $response;
}

function validationerror($response) {
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(422, "Validation Error");
    return $response;
}

function getServerID($api){
    $conn = PDOConnection::getConnection();
    $sql = "SELECT id FROM servers WHERE apikey = :apikey AND enabled=1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":apikey", $api);
    $stmt->execute();

    $query = $stmt->fetch();

    return $query['id'];
}

function isValidKey($key, $ip){
    $conn = PDOConnection::getConnection();
    $sql = "SELECT * FROM servers WHERE apikey = :apikey AND ip = :ip AND enabled=1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":apikey", $key);
    $stmt->bindParam(":ip", $ip);
    $stmt->execute();

    $query = $stmt->fetchObject();

    return $query;
}

function isValidInstallKey($key){
    $conn = PDOConnection::getConnection();
    $sql = "SELECT * FROM installkeys WHERE installkey = :key";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":key", $key);
    $stmt->execute();

    $query = $stmt->fetchObject();

    return $query;
}

function isValidAPIKey($api){
    $conn = PDOConnection::getConnection();
    $sql = "SELECT * FROM apis WHERE api = :api AND	enabled = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":api", $api);
    $stmt->execute();

    $query = $stmt->fetchObject();

    return $query;
}

function isUsernameTaken($username){

    // $stmt->execute();
    // $result = $stmt->fetch();
    //
    // if (is_null($result["id"])) {
    //     return ok($response);
    // } else {
    //     return error($response);
    // }
    $conn = PDOConnection::getConnection();
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $query = $stmt->fetchObject();

    return $query;
}




function isValidAPIRequest($api, $ip){
    if(empty($api)) return false;
    $conn = PDOConnection::getConnection();
    $sql = "SELECT * FROM apis WHERE api = :api AND	ip = :ip AND enabled = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":api", $api);
    $stmt->bindParam(":ip", $ip);
    $stmt->execute();

    $query = $stmt->fetchObject();

    return $query;
}

function realIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    return strval($ip_address);
}

function createUsername($username, $n){
    if(!isUsernameTaken($username)) return $username;

    if(substr($username, -(strlen($n))) == $n || $n==1) {
        $usernameno = (substr($username, -(strlen($n))) == $n) ? substr($username, 0, -(strlen($n))) : $username;
        if(substr($username, -(strlen($n))) == $n) $n++;
        $username = $usernameno.$n;
    }
    return createUsername($username, $n);
}
?>
