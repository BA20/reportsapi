<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app->get('/api/users', function(Request $request, Response $response, $args){
    require_once('dbconnect.php');
    foreach($db->user()
        as $row){
        
        $data[] = $row;
    }

    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    return $response;
}); 
$app->get('/api/reports', function(Request $request, Response $response, $args){
    require_once('dbconnect.php');
    foreach($db->report()
    ->order("idreports")
    as $row){
        $data[] = $row;
    }

    echo json_encode($data,JSON_UNESCAPED_UNICODE);
    return $response;
}); 

$app->get('/api/reportsAll', function(Request $request, Response $response, $args){
    require_once('dbconnect.php');
    $report = array();
    $user = $db->user(); 
    foreach($db->report()
    
    as $row){
        array_push($report, array(
            'id' => $row["id"],
            'titulo' => $row["titulo"],
            'descricao' => $row["descricao"],
            'lat' => $row["lat"],
            'lng' => $row["lng"],
            'user_id'=> $row["user_id"],
            'tipo_id'=> $row["tipo_id"],
            'email' => $row->user["email"],
            'descr' => $row->tipo["descr"]
        ));
    }

    echo json_encode($report,JSON_UNESCAPED_UNICODE);
    return $response;
}); 

$app->post('/api/reportsUpdate',function(Request $request, Response $response, $args){
    require_once('dbconnect.php');
    $id= $_POST["id"];
    $titulo= $_POST["titulo"];
    $descricao= $_POST["descricao"];
    $data = array(
        "titulo" => $titulo,"descricao" => $descricao
    );
    VAR_DUMP($titulo);
    var_dump($data);
    var_dump($id);

    if(isset($db->report[$id])){
        $result = $db->report[$id]->update($data);
        if($result){
            echo json_encode("Editado com sucesso!",JSON_UNESCAPED_UNICODE);
        }
        else{
            echo json_encode("Sem sucesso!",JSON_UNESCAPED_UNICODE);
        }
    }else{
        echo json_encode("Não existe Report!",JSON_UNESCAPED_UNICODE);
    }
  
})



?>