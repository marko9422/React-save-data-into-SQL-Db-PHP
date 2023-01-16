<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

include 'DBconnection.php';
$objDb = new DbConnect;
$conn = $objDb->connect();


$method = $_SERVER['REQUEST_METHOD'];
switch($method) {

    case "GET":
        $sql = "SELECT * FROM users";
        $path = explode('/', $_SERVER['REQUEST_URI'] ); 

        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $users = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($users);
        break;

    case "POST":
        $user = json_decode( file_get_contents('php://input') );
        $sql = "INSERT INTO users(id, name, surename, phone, created_at) 
        VALUES(null, :name, :surename, :phone, :created_at)";
        $stmt = $conn->prepare($sql);

        $created_at = date('Y-m-d');
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':surename', $user->surename);
        $stmt->bindParam(':phone', $user->phone);
        $stmt->bindParam(':created_at', $created_at);

        if($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record created successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record.'];
        }

        echo json_encode($users);
        break;

        
        case "PUT":
            $user = json_decode( file_get_contents('php://input') );
            $sql = "UPDATE users SET name= :name, surename =:surename, phone =:phone, edited_at =:edited_at WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $edited_at = date('Y-m-d');
            $stmt->bindParam(':id', $user->id);
            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':surename', $user->surename);
            $stmt->bindParam(':phone', $user->phone);
            $stmt->bindParam(':edited_at', $edited_at);
    
            if($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record edited successfully.'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to update record.'];
            }
            echo json_encode($response);
            break;

        case "DELETE":
            $sql = "DELETE FROM users WHERE id = :id";
            $path = explode('/', $_SERVER['REQUEST_URI'] ); 
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            
            if($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to deleted record.'];
            }

            echo json_encode($users);
            break;
}