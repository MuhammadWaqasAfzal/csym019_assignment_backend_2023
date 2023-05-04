<?php
include('dbcon.php');


function registerUser($userData)
{
    global $conn;

    $userName = $userData['userName'];
    $email = $userData['email'];
    $password =  $userData['password'];
    $confirmPassowrd = $userData['confirmPassword'];

   
    if (empty(trim($userName))) {
        return response("422", "HTTP/1.0 422 User name is required", "User name is required");
    } else if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    } else if (empty(trim($password))) {
        return response("422", "HTTP/1.0 422 Password is required", "Password is required");
    } else if (empty(trim($confirmPassowrd))) {
        return response("422", "HTTP/1.0 422 Confirm password is required", "Confirm password is required");
    } else if ((trim($password)) !== trim($confirmPassowrd)) {
        return response("422", "HTTP/1.0 422 Passwords do not match", "Passwords do not match");
    }
    else {
        $userExits = "SELECT * FROM users WHERE email = '$email'";
        $query_run = $conn->query($userExits);
        if (mysqli_num_rows($query_run) > 0) {
            $data = [
                'status' => 404,
                'message' => "Email Already Exits",
            ];
            header("HTTP/1.0 404 Email Already Exits");
            return json_encode($data);
        } else {
            $query = "INSERT INTO users (userName,password,email)
            VALUES ('$userName','$password','$email')";
            $query_run = mysqli_query($conn, $query);           
            if ($query_run) {
                $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password'";
                // $result = $conn -> query($userExits);
                $result = mysqli_query($conn, $query);
                if ($result) {
                    $res = mysqli_fetch_assoc($result);
                    $data = [
                        'status' => 201,
                        'message' => "User Registered Successfully",
                        'data' => $res
                    ];
                    header("HTTP/1.0 201 Success");
                    return json_encode($data);
                } else {
                    return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
                }
                // return response("201", "HTTP/1.0 201 Created", "User Registered Successfully");
            } else {
                return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
            }
        }
    }
}

function loginUser($userData)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $userData['email']);
    $password = mysqli_real_escape_string($conn, $userData['password']);


    if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    } else if (empty(trim($password))) {
        return response("422", "HTTP/1.0 422 Password is required", "Password is required");
    } else {
        $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password'";
                $result = mysqli_query($conn, $query);
                $res = mysqli_fetch_assoc($result);
                $data = [
                    'status' => 200,
                    'message' => "User logged in Successfully",
                    'data' => $res
                ];
                header("HTTP/1.0 200 Success");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 401,
                    'message' => "Invalid username or password",
                ];
                header("HTTP/1.0 401 Invalid username or password");
                return json_encode($data);
            }
        } else {
            return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
        }
    }
}

function resetPassword($userData)
{
    global $conn;

    $email = mysqli_real_escape_string($conn, $userData['email']);
    $password = mysqli_real_escape_string($conn, $userData['password']);
    $confirmPassowrd = mysqli_real_escape_string($conn, $userData['confirmPassword']);

    if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    } else if (empty(trim($password))) {
        return response("422", "HTTP/1.0 422 Password is required", "Password is required");
    } else if (empty(trim($confirmPassowrd))) {
        return response("422", "HTTP/1.0 422 Confirm password is required", "Confirm password is required");
    } else if ((trim($password)) !== trim($confirmPassowrd)) {
        return response("422", "HTTP/1.0 422 Passwords do not match", "Passwords do not match");
    } else {
        $userExits = "SELECT * FROM Users WHERE email = '$email'";
        $query_run = $conn->query($userExits);
        if (mysqli_num_rows($query_run) > 0) {
            $query_run = null;
            $query = "UPDATE `Users` SET `Password`='$password' WHERE Email = '$email'";
            $query_run = mysqli_query($conn, $query);
            if ($query_run) {
                return response("200", "Password Updated Successfully", "Password Updated Successfully");
            } else {
                return response("500", "Internal Server Error", "Internal Server Error");
            }
        } else {
            return response("404", "HTTP/1.0 404 Invalid Email", "Invalid Email");
        }
    }
}



function logOutUser($userData)
{
    
    global $conn;
   
    $email = mysqli_real_escape_string($conn, $userData['email']);
   
    if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    }else {
        $userExits = "SELECT * FROM Users WHERE email = '$email'";
        $query_run = $conn->query($userExits);
        if (mysqli_num_rows($query_run) > 0) {
            $query_run = null;
            $query = "UPDATE `Users` SET `FCMToken`=NULL WHERE Email = '$email'";
            $query_run = mysqli_query($conn, $query);
            if ($query_run) {
                $data = [
                    'status' => 200,
                    'message' => "User logged out Successfully",
                ];
                header("HTTP/1.0 200 Success");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 500,
                    'message' => "Internal Server Error",
                ];
                header("HTTP/1.0 500 Failure");
                return json_encode($data);
            }
        } else {
            return response("404", "HTTP/1.0 404 Invalid Email", "Invalid Email");
        }
    }
}

function addCourse($courseData)
{
    global $conn;
  
    $title = mysqli_real_escape_string($conn, $courseData['title']);
    $level = mysqli_real_escape_string($conn, $courseData['level']);
    $full_time_duration = mysqli_real_escape_string($conn, $courseData['full_time_duration']);
    $full_time_with_placement_duration = mysqli_real_escape_string($conn, $courseData['full_time_with_placement_duration']);
    $full_time_foundation_duration = mysqli_real_escape_string($conn, $courseData['full_time_foundation_duration']);
    $part_time_duration = mysqli_real_escape_string($conn, $courseData['part_time_duration']);
    $start = mysqli_real_escape_string($conn, $courseData['start']);
    $location = mysqli_real_escape_string($conn, $courseData['location']);
    $overview = mysqli_real_escape_string($conn, $courseData['overview']); 
    $modules = ($courseData['modules']);
    $fees = ($courseData['fees']);
    $entryRequirements = mysqli_real_escape_string($conn,($courseData['entryRequirements']));

  

  
    if(empty(trim($title))) {
        return response("422", "HTTP/1.0 422 Course title is required", "Course title is required");
    } else if (empty(trim($level))) {
        return response("422", "HTTP/1.0 422 Course level cannot be empty", "Course level cannot be empty");
    } else if (empty(trim($full_time_duration))) {
        return response("422", "HTTP/1.0 422 Full Time Duration is required", "Full Time Duration is required");
    } else if (empty(trim($full_time_with_placement_duration))) {
        return response("422", "HTTP/1.0 422 Full Time with Placement Duration is required", "Full Time with Placement Duration is required");
    } else if (empty(trim($full_time_foundation_duration))) {
        return response("422", "HTTP/1.0 422 Full Time Foundation Duration is required", "Full Time Foundation Duration is required");
    } else if (empty(trim($part_time_duration))) {
        return response("422", "HTTP/1.0 422 Part Time Duration is required", "Part Time Duration is required");
    } else if (empty(trim($start))) {
        return response("422", "HTTP/1.0 422 Course start is required", "Course start is required");
    } else if (empty(trim($location))) {
        return response("422", "HTTP/1.0 422 Course location is required", "Course location is required");
    } else if (empty(trim($overview))) {
        return response("422", "HTTP/1.0 422 Course overview is required", "Course overview is required");
    }
    else {

            $query = "INSERT INTO `courses`(`title`, `full_time_duration`, `full_time_with_placement_duration`,
             `full_time_foundation_duration`, `part_time_duration`, `start`, `location`, `overview`, `level`,`entry_requirements`) 
                    VALUES ('$title','$full_time_duration','$full_time_with_placement_duration','$full_time_foundation_duration','$part_time_duration','$start','$location','$overview','$level','$entryRequirements')";          
           // echo ($query);
            $query_run = mysqli_query($conn, $query);
            //return response("201", "HTTP/1.0 201 Course added Successfully", "Course added Successfully");

            //echo ($query_run);
            if ($query_run) {
               
                //foreign key for modules and fees tables
                $lastinnsertedId=strval(mysqli_insert_id($conn));
                $values = array();
                foreach ($modules as $item) {
                    $values[] = "('{$lastinnsertedId}','{$item['category']}','{$item['name']}','{$item['credit_hours']}','{$item['code']}','{$item['status']}','{$item['pre_requisites']}')";
                }

                $values = implode(", ", $values);
                $query = "INSERT INTO `modules`(`course_id`,`category`, `name`, `credit_hours`, `code`, `status`, `pre_requisites`) VALUES {$values}";
                $query_run = mysqli_query($conn, $query);
               
                if ($query_run) {
                    $feeValues = array();
                    foreach ($fees as $item) {
                        $feeValues[] = "('{$lastinnsertedId}','{$item['session']}','{$item['uk_full_time_fee']}','{$item['uk_part_time_fee']}','{$item['uk_international_foundation_year']}','{$item['international_full_year_fee']}','{$item['international_integrated_foundation_year_fee']}','{$item['placement_fee']}','{$item['additional_cost']}')";
                    }
                    $feeValues = implode(", ", $feeValues);
                    $query = "INSERT INTO `fees`(`course_id`, `session`, `uk_full_time_fee`, `uk_part_time_fee`, `uk_international_foundation_year`, `international_full_year_fee`,`international_integrated_foundation_year_fee`, `placement_fee`, `additional_cost`) VALUES {$feeValues}";
                    $query_run = mysqli_query($conn, $query);
                    if ($query_run) {
                        return response("201", "HTTP/1.0 201 Course added Successfully", "Course added Successfully");
                    }else{
                        return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
                    }
                }else{                
                    return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
                }           
            } else {
                return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
            }
       
    }
    
}



function getAllCourses($data)
{
    global $conn;
    $query = "SELECT * FROM courses";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
       
        for ($x = 0; $x < count($res); $x++) {
            $row = $res[$x];
            $courseId = $row["Id"];
            $query = "SELECT * FROM modules where course_id = '$courseId'";
            $query_run = mysqli_query($conn, $query);
            $modulesRes = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            $res[$x]["modules"] = $modulesRes; 
            $query = "SELECT * FROM fees where course_id = '$courseId'";
            $query_run = mysqli_query($conn, $query);
            $feesRes = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            $res[$x]["fees"] = $feesRes; 
        }
        
        $data = [
            'status' => 200,
            'message' => "All Courses",
            'data' => $res
        ];
        header("HTTP/1.0 200 Success");
        return json_encode($data);
    } else {
        return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
    }
}



function response($statusCode, $header, $message)
{
    $data = [
        'status' => $statusCode,
        'message' => $message,
    ];
    header($header);
    echo json_encode($data);
    exit();
}
