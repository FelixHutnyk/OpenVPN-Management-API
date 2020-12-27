<?php
function ok($response, $status="OK") {
    $data['status'] = $status;
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(200, "OK")
    ->withJson($data);
    return $response;
}

function error($response, $status="Conflict") {
    $data['status'] = $status;
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(409, "Conflict")
    ->withJson($data);
    return $response;
}

function validationerror($response) {
    $response = $response->withHeader("Content-Type", "application/json")
    ->withStatus(422, "Validation Error");
    return $response;
}

?>
