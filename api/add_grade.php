<?php

//print_r($_POST);
$output = [
    'message' => 'testing add record',
    'record' => null 
];



$course = null;
$grade = null;
$name = null; 
$errors = [];

if(isset($_POST['course'])){
    $course = $_POST['course'];
}else {
    $errors[] = 'No course name received';
}

if(isset($_POST['grade'])){
    $grade = (int) $_POST['grade']; 

    if($grade <0 || $grade > 100){
        $errors[] = "Grade must be between 1 and 100: $grade is invalid";
    }
}else {
    $errors[] = 'No grade received';
}

if(isset($_POST['name'])){
    $name = $_POST['name'];
}else {
    $errors[] = 'No name received';
}

if(count($errors) > 0){
    http_response_code(422); 
    echo json_encode([
        'errors' =>  $errors,
        'message' => "Unable tp add new grade"
    ]);
    exit;
}

//$sql =  "INSERT INTO grades (pid, course, grade, name) VALUES (UUID(), '$course', $grade, '$name'";

$result = mysqli_query($conn,"INSERT INTO grades (pid, course, grade, name) VALUES (UUID(), '$course', $grade, '$name')");

if(mysqli_affected_rows($conn) >0){
    $record_id = mysqli_insert_id($conn);
    $result = mysqli_query($conn, "
        SELECT pid, course, grade, name, updated AS lastUpdated
        FROM grades WHERE id=$record_id");
        if(mysqli_num_rows($result)){
            $output['message'] = "Successfully added new record for $name";
            $output['record'] = mysqli_fetch_assoc($result);

            echo json_encode($output);
            exit;
        }
                 
}

http_response_code(500);
echo json_encode([
    'errors' => ['Error adding grade record']
]);


