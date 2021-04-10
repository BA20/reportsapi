<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app->get('/api/users', function(Request $request, Response $response, $args){
    require_once('dbconnect.php');
    foreach($db->user() as $row){
        $data[] = $row;
    }

    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    return $response;
}); 
?>