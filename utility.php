<?php
include('dbcon.php');

/* 
This fucntion register new user in databse 
*/
function registerUser($userData)
{
    global $conn; // global object for database connection.

    // getting values from data passed to api.
    $userName = $userData['userName'];
    $email = $userData['email'];
    $password =  $userData['password'];
    $confirmPassowrd = $userData['confirmPassword'];

    // validatios for all fields .
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
    } else {
        //checking if email already exists
        $userExits = "SELECT * FROM users WHERE email = '$email'";
        $query_run = $conn->query($userExits);
        if (mysqli_num_rows($query_run) > 0) {
            // returnning reposne that email already exists.
            $data = [
                'status' => 404,
                'message' => "Email Already Exits",
            ];
            header("HTTP/1.0 404 Email Already Exits");
            return json_encode($data);
        } else {
            // inserting user into databse
            $query = "INSERT INTO users (userName,password,email)
            VALUES ('$userName','$password','$email')";
            $query_run = mysqli_query($conn, $query);
            if ($query_run) {
                // getting all data of newely added user and returning back to user.
                $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password'";
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
            } else {
                return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
            }
        }
    }
}

/* 
This fucntion login user in to application 
*/
function loginUser($userData)
{
    global $conn; // global object for database connection.

    // getting values from data passed to api.
    $email = mysqli_real_escape_string($conn, $userData['email']);
    $password = mysqli_real_escape_string($conn, $userData['password']);

    // validatios for all email and password 
    if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    } else if (empty(trim($password))) {
        return response("422", "HTTP/1.0 422 Password is required", "Password is required");
    } else {
        // checking user exists in data base.  
        $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                // getting user information from databse to send back in response.
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
                // if user not found in data base, returing reposne to user.
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

/* 
This fucntion  resets password of user
*/
function resetPassword($userData)
{
    global $conn; // global object for database connection

    // getting values from data passed to api.
    $email = mysqli_real_escape_string($conn, $userData['email']);
    $password = mysqli_real_escape_string($conn, $userData['password']);
    $confirmPassowrd = mysqli_real_escape_string($conn, $userData['confirmPassword']);

    // validatios for all fields.
    if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    } else if (empty(trim($password))) {
        return response("422", "HTTP/1.0 422 Password is required", "Password is required");
    } else if (empty(trim($confirmPassowrd))) {
        return response("422", "HTTP/1.0 422 Confirm password is required", "Confirm password is required");
    } else if ((trim($password)) !== trim($confirmPassowrd)) {
        return response("422", "HTTP/1.0 422 Passwords do not match", "Passwords do not match");
    } else {

        // checking user exists in data base.  
        $userExits = "SELECT * FROM users WHERE email = '$email'";

        $query_run = $conn->query($userExits);

        if (mysqli_num_rows($query_run) > 0) { // if user exists, then moving forward

            $query_run = null;

            // updating user password
            $query = "UPDATE `users` SET password='$password' WHERE email = '$email'";

            $query_run = mysqli_query($conn, $query);

            // on query success returning respone to user.
            if ($query_run) {
                return response("200", "Password Updated Successfully", "HTTP/1.0 Password Updated Successfully");
            } else {
                return response("500", "Internal Server Error", "HTTP/1.0 Internal Server Error");
            }
        } else {
            return response("404", "HTTP/1.0 404 Invalid Email", "Invalid Email");
        }
    }
}


/* 
This fucntion delete course from databse
*/
function deleteCourse($courseData)
{

    global $conn; // global object for database connection.

    // getting values from data passed to api.
    $courseId = mysqli_real_escape_string($conn, $courseData['course_id']);

    // validatios for course id.
    if (empty(trim($courseId))) {
        return response("422", "HTTP/1.0 422 Email is required", "Course Id is required");
    } else {
        $courseExits = "DELETE  FROM fees WHERE course_id = '$courseId'";
        $query_run = $conn->query($courseExits);
        $courseExits = "DELETE  FROM modules WHERE course_id = '$courseId'";
        $query_run = $conn->query($courseExits);
        if ($query_run) {
            $deleteCourse = "DELETE FROM `courses` WHERE  Id = '$courseId'";
            $query_run = $conn->query($deleteCourse);

            // on query success returning respone to user.
            if ($query_run) {
                return response("200", "HTTP/1.0 200 Course Deleted Successfully", "Course Deleted Successfully");
            } else {
                return response("500", "HTTP/1.0 500 Internal Server Error", "Internal Server Error");
            }
        } else {
            return response("404", "HTTP/1.0 404 Invalid Course Id", "Invalid Course Id");
        }
    }
}

/* 
This fucntion add course in databse
*/
function addCourse($courseData)
{
    global $conn; // global object for database connection

    // getting values from data passed to api.
    $title = mysqli_real_escape_string($conn, $courseData['title']);
    $level = mysqli_real_escape_string($conn, $courseData['level']);
    $full_time_duration = mysqli_real_escape_string($conn, $courseData['full_time_duration']);
    $full_time_with_placement_duration = mysqli_real_escape_string($conn, $courseData['full_time_with_placement_duration']);
    $full_time_foundation_duration = mysqli_real_escape_string($conn, $courseData['full_time_foundation_duration']);
    $part_time_duration = mysqli_real_escape_string($conn, $courseData['part_time_duration']);
    $start = ($courseData['start']);
    $location = mysqli_real_escape_string($conn, $courseData['location']);
    $overview = mysqli_real_escape_string($conn, $courseData['overview']);
    $modules = ($courseData['modules']);
    $fees = ($courseData['fees']);
    $entryRequirements = mysqli_real_escape_string($conn, ($courseData['entryRequirements']));


    // validatios for all fields.
    if (empty(trim($title))) {
        return response("422", "HTTP/1.0 422 Course title is required", "Course title is required");
    } else if (empty(trim($level))) {
        return response("422", "HTTP/1.0 422 Course level cannot be empty", "Course level cannot be empty");
    } else if (empty(trim($full_time_duration))) {
        return response("422", "HTTP/1.0 422 Full Time Duration is required", "Full Time Duration is required");
    } else if (empty(trim($start))) {
        return response("422", "HTTP/1.0 422 Course start is required", "Course start is required");
    } else if (empty(trim($location))) {
        return response("422", "HTTP/1.0 422 Course location is required", "Course location is required");
    } else if (empty(trim($overview))) {
        return response("422", "HTTP/1.0 422 Course overview is required", "Course overview is required");
    } else {

        $query = "INSERT INTO `courses`(`title`, `full_time_duration`, `full_time_with_placement_duration`,
             `full_time_foundation_duration`, `part_time_duration`, `start`, `location`, `overview`, `level`,`entry_requirements`) 
                    VALUES ('$title','$full_time_duration','$full_time_with_placement_duration','$full_time_foundation_duration','$part_time_duration','$start','$location','$overview','$level','$entryRequirements')";


        try {
            $query_run = mysqli_query($conn, $query);
            if ($query_run) {

                //foreign key for modules and fees tables
                $lastinnsertedId = strval(mysqli_insert_id($conn));
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
                        $feeValues[] = "('{$lastinnsertedId}','{$item['session']}','{$item['uk_full_time_fee']}','{$item['uk_part_time_fee']}','{$item['uk_part_time_year']}','{$item['uk_part_time_per_credit_hour']}','{$item['uk_part_time_total_credit_hours']}','{$item['uk_international_foundation_year']}','{$item['international_full_year_fee']}','{$item['international_integrated_foundation_year_fee']}','{$item['placement_fee']}','{$item['additional_cost']}')";
                    }

                    $feeValues = implode(", ", $feeValues);
                    $query = "INSERT INTO `fees` (`course_id`, `session`, `uk_full_time_fee`, `uk_part_time_fee`,`uk_part_time_year`,`uk_part_time_per_credit_hour`,`uk_part_time_total_credit_hours`, `uk_international_foundation_year`, `international_full_year_fee`,`international_integrated_foundation_year_fee`, `placement_fee`, `additional_cost`) VALUES {$feeValues}";

                    $query_run = mysqli_query($conn, $query);

                    // on query success returning respone to user.
                    if ($query_run) {
                        return response("201", "HTTP/1.0 201 Course added Successfully", "Course added Successfully");
                    } else {
                        return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
                    }
                } else {
                    return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
                }
            } else {
                return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
            }
        } catch (mysqli_sql_exception $e) {
            // Handle the unique constraint error
            if ($e->getCode() === 1062) {
                return response("1062", "Course Name Already Exists.", "HTTP/1.0 1062 Course Name Already Exists.");

                // Additional error handling or actions
            } else {
                // Handle other types of exceptions or errors
                echo "An error occurred: " . $e->getMessage();
            }
        }
    }
}


/* 
This fucntion update course in databse
*/
function updateCourse($courseData)
{
    global $conn; // global object for database connection

    // getting values from data passed to api.
    $courseId = mysqli_real_escape_string($conn, $courseData['id']);

    // validatios for course id.
    if (empty(trim($courseId))) {
        return response("422", "HTTP/1.0 422 Course Id is required", "Course Id is required");
    } else {

        $deleteModules = "DELETE FROM `modules` WHERE  Id = '$courseId'";
        $query_run = $conn->query($deleteModules);

        $deleteFees = "DELETE FROM `fees` WHERE  Id = '$courseId'";
        $query_run = $conn->query($deleteFees);


        $deleteCourse = "DELETE FROM `courses` WHERE  Id = '$courseId'";
        $query_run = $conn->query($deleteCourse);
        if ($query_run) { // on query success returning respone to user.
            addCourse($courseData);
            return response("200", "HTTP/1.0 200 Course Updated Successfully", "Course Updated Successfully");
        } else {
            return response("500", "HTTP/1.0 500 Internal Server Error", "Internal Server Error");
        }
    }
}

/* 
This fucntion gets all courses from data base and return iot back to user.
*/
function getAllCourses($data)
{
    global $conn; // global object for database connection

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
        // on query success returning respone to user.
        return json_encode($data);
    } else {
        return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
    }
}


/* 
This fucntion creates response in a proper fomrmat
*/
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


/* 
This fucntion edits user name in databse
*/
function editUserName($userData)
{
    global $conn; // global object for database connection

    // getting values from data passed to api.
    $email = mysqli_real_escape_string($conn, $userData['email']);
    $userName = mysqli_real_escape_string($conn, $userData['userName']);
    $password = mysqli_real_escape_string($conn, $userData['password']);


    // validatios for all fields.
    if (empty(trim($userName))) {
        return response("422", "HTTP/1.0 422 User name is required", " User name is required");
    } else if (empty(trim($email))) {
        return response("422", "HTTP/1.0 422 Email is required", "Email is required");
    } else {
        $query = "UPDATE `users` SET `userName`='$userName' , `password`='$password' WHERE email = '$email'";
        $query_run = mysqli_query($conn, $query);
        if ($query_run) {
            // on query success returning respone to user.
            $query = "SELECT * FROM Users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);
            $res = mysqli_fetch_assoc($result);
            $data = [
                'status' => 200,
                'message' => "User name updated Successfully",
                'data' => $res
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        } else {
            return response("500", "Internal Server Error", "HTTP/1.0 500 Internal Server Error");
        }
    }
}
