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
$app->get('/api/tipo', function(Request $request, Response $response, $args){
    require_once('dbconnect.php');
    foreach($db->tipo()
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
            'tipo'=> $row["tipo"],
            'imagem'=>$row["imagem"],
            'username' => $row->user["username"]
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
        echo json_encode("N??o existe Report!",JSON_UNESCAPED_UNICODE);
    }
  
});

$app->post('/api/userRegister', function ($request, $response) {
    require_once('dbconnect.php');
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    $pwenc = password_hash($pass, PASSWORD_BCRYPT);
    $data = array("username" => $user, "password" => $pwenc);
    $user = $db->user();

    foreach ($db->user()
        ->where('username', $user)
        as $row) {
        $teste[] = $row;
    }
    if (!isset($teste)) {
        $result = $user->insert($data);
        if ($result == false) {
            $result = ['status' => false, 'msg' => "Registo falhado!"];
        } else {
            $result = ['status' => true, 'msg' => "Registo inserido com o id " . $result["id"]];
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode("Username j?? existente!", JSON_UNESCAPED_UNICODE);
    }

    return $response;
});
$app->post('/api/user', function ($request, $response) {
    require_once('dbconnect.php');
    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach ($db->user()
        ->where('username', $username)
        as $row) {
        $teste = $row;
    }

    if (isset($teste)) {
        if (password_verify($password, $teste['password'])) {
            $data = ['status' => true, 'MSG' => "Login realizado com sucesso", 'id' => $teste['id']];
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
            return $response;
        } else {
            $data = ['status' => false, 'MSG' => "Password ou Username incorretos"];
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
            return $response;
        }
    } else {
        $data = ['status' => false, 'MSG' => "Utilizador nao existe"];
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response;
    }
    return $response;
});
function getRandomFilename() { 
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = ''; 

        for ($i = 0; $i < 32; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 

        return $randomString; 
    }

$app->post('/api/createR', function ($request,$response) {
    require_once('dbconnect.php');


    $titulo = $_POST["titulo"];
    $descricao = $_POST["descricao"];
    $lat = $_POST["lat"];
    $lng= $_POST["lng"];
    $user_id = $_POST["user_id"];
    $tipo = $_POST["tipo"];



    $data = array(
        "titulo" =>$titulo,
        "descricao" => $descricao,
        "lat" => $lat,
        "lng" => $lng,
        "user_id" => $user_id,
        "tipo" => $tipo
    );

    if(isset($_FILES['imagem'])){

            $data['imagem'] = getRandomFilename();

            move_uploaded_file($_FILES['imagem']['tmp_name'], '/storage/ssd2/130/16565130/public_html/myslim/api/img/' . $data['imagem'] . '.png');
      }


   $reports= $db->report();
   $result = $reports->insert($data);

    if($result == false){
        $result= [ 'status' => false, 'MSG' => 'Inser??ao falhou'];
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }else{
        $result= [ 'status' => true, 'MSG' => 'Inser??ao ocorreu'];
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    return $response;
});
$app->post('/api/updateR', function ($request,$response) {
    require_once('dbconnect.php');

    $id = $_POST["id"];

    $titulo = $_POST["titulo"];
    $descricao = $_POST["descricao"];

    if(empty($titulo)){
        $data = array(
            "descricao" => $descricao,
        );
    }

    if(empty($descricao)){
        $data = array(
            "titulo" => $titulo,
        );
    }

    if(!empty($titulo) && !empty($descricao)){
        $data = array(
            "titulo" =>$titulo,
            "descricao" => $descricao,
        );
    }


   if(isset($db->report[$id])){
       $result=$db->report[$id]->update($data);

       if($result == false){
         $result= [ 'status' => false];
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
       }else{
          $result= [ 'status' => true];
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
       }

   }else{

      $result= [ 'status' => false, 'MSG' => 'Registo nao existe'];
      echo json_encode($result,JSON_UNESCAPED_UNICODE);
   }


    return $response;
});
$app->post('/api/deleteReport/{id}', function ($request, $response){
     require_once('dbconnect.php');

    $id = $request->getAttribute('id');

    $ocorr = $db->report[$id];

    if($ocorr){
         $result = $ocorr->delete();

        if($result){
            $result= [ 'status' => true, 'MSG' => 'Registo eliminado'];
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        else{
            $result= [ 'status' => false, 'MSG' => 'Registo nao eliminado'];
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }else {

            $result= [ 'status' => false, 'MSG' => 'Registo nao existe'];
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    return $response;
});

?>