<?php
include_once("conn.php");

$method=$_SERVER["REQUEST_METHOD"];

if($method==="GET"){

    $bordasQuery=$conn->query("SELECT * FROM  pizzaria.bordas;");
    $bordas= $bordasQuery->fetchAll();

    $massasQuery=$conn->query("SELECT*FROM  pizzaria.massas;");
    $massas= $massasQuery->fetchAll();

    $saboresQuery=$conn->query("SELECT*FROM  pizzaria.sabores;");
    $sabores= $saboresQuery->fetchAll();

}
else if($method==="POST"){

    $data=$_POST;

    $borda=$data["borda"];
    $massa=$data["massa"];
    $sabores=$data["sabores"];

    if(count($sabores)>3){

        $_SESSION["msg"]="Selecione no máximo 3 sabores";
        $_SESSION["status"]="warning";

    }else{
    
        // salvando borda e massa na pizza
      $stmt = $conn->prepare("INSERT INTO pizzaria.pizzas (borda_id, massa_id) VALUES (:borda, :massa)");

      // filtrando inputs
      $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
      $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

      $stmt->execute() ;

      // resgatando último id da última pizza
      $pizzaId = $conn->lastInsertId();

      $stmt = $conn->prepare("INSERT INTO pizzaria.pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

      // repetição até terminar de salvar todos os sabores
      foreach($sabores as $sabor) {

        // filtrando os inputs
        $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
        $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);

        $stmt->execute() ;
      }

        $stmt = $conn->prepare("INSERT INTO pizzaria.pedidos (pizza_id, status_id) VALUES (:pizza, :status)");

        $statusId=1;

        $stmt->bindParam(":pizza", $pizzaId);
        $stmt->bindParam(":status", $statusId);

        $stmt->execute() ;


        $_SESSION["msg"]="Pedido realizado com sucesso";
        $_SESSION["status"]="Sucess";
    }

    header("Location:..");
}
?>