<?php
/* Create Project 
 * admin page
 * requires logged on
*/
require_once 'env/environment.php';
require_once 'functions/functions.php';
require_once 'class/PDODB.php';
require_once 'functions/project-functions.php';
session_start();

// Attempt to make connection to the database
$dbConn = PDODB::getConnection();

// Check for logged in user
$loggedIn = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Page title
$pageTitle = 'Create Project';  

// Check the user role of the logged in user
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Get the correcct page header depending on the users current role
// If user is not logged in display message to user telling them to log in
if (!isset($loggedIn)){
  echo getHTMLHeader($pageTitle, $loggedIn);
  $errorText = "Sorry you must be logged to access this page. Please login <a href='login.php'>here</a> to login to your account. If you don't currently have an account please contact your system administrator to have one created for you.";
  
} elseif (isset($userRole) && $userRole == 2){        // User level 
  echo getHTMLUserHeader($pageTitle, $loggedIn);
  $errorText = "Sorry you do not have the correct permissions to access this page. Please select a different role <a href='select-role.php'>here</a> to change your account role.";

} elseif (isset($userRole) && $userRole == 1){        // Admin level
  echo getHTMLAdminHeader($pageTitle, $loggedIn);
    
} else{
  echo getHTMLHeader($pageTitle, $loggedIn);
  $errorText = "Sorry you do not have the correct permissions to access this page. Please select a different role <a href='select-role.php'>here</a> to change your account role.";
}
  
?>

    <div class="jumbotron text-center">
        <h1><?php echo $pageTitle; ?></h1>
    </div>
<?php

// Check if project has been submitted from form
if (isset($_REQUEST['project'])) {
    // Add the project to the $input array
    $input = array();
    $input['project'] = isset($_POST['project']) ? $_POST['project'] : null;

    // Run the SQL
    if (createProject($dbConn, $input)) {
        $querySuccessMsg = "<div class= 'row justify-content-center align-items-center'>
                                <h3>You have successfully created a new Project</h3>
                            </div>";
    }
}


// Show success message if query ran/succeeded
if (isset($querySuccessMsg)) {
    echo($querySuccessMsg);
    // Else display form
} else {
    echo('
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <form action="create-project.php" name="createProjectForm" method="POST">
                    <h3 class="text-center">Enter the project details</h3>
                    <div class="form-group">
                        <label for="project">Project:</label>
                        <input type="text" class="form-control" placeholder="Project" name="project" required/>
                    </div>
                    <div class="login-error">
    ');
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class=\'error\'>$error</p>";

        }
    }
    echo('
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
    ');
}


?>

<?php
getHTMLEnd();