<?php
/* Delete Department
 * admin page
 * requires logged on
*/
require_once 'env/environment.php';
require_once 'functions/functions.php';
require_once 'functions/department-functions.php';
require_once 'class/PDODB.php';
session_start();

// Attempt to make connection to the database
$dbConn = PDODB::getConnection();

// Check for logged in user
$loggedIn = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Page title
$pageTitle = 'Delete Department';  

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

// Check to see if a valid department id has been passed in
if(isset($_REQUEST['department_id'])){
    // Validation Checks
    if(is_numeric($_REQUEST['department_id'])){
        if(!empty(getDepartment($dbConn, $_REQUEST['department_id']))){
            $department_name = getDepartment($dbConn, $_REQUEST['department_id']);
        
        } else{
            $errorText = "You have not chosen a valid department to delete.  Please select a department <a href='manage-department.php'>here</a> to remove it from the system";
        
        }

    } else {
        $errorText = "You have not chosen a valid department to delete.  Please select an department <a href='manage-department.php'>here</a> to remove it from the system";
   
    }

} else{
    $errorText = "You have not chosen a valid department to delete.  Please select an department <a href='manage-department.php'>here</a> to remove it from the system";
}

?>
<div class="jumbotron text-center">
  <h1><?php echo $pageTitle;?></h1>
</div>

<div class="container">
  <div class="row justify-content-center align-items-center">
    <?php

    // If not logged in show text
    if (isset($errorText)){
      echo "<p>$errorText</p>";

    } else if (isset($_POST['department_id'])){
      // Validate
      list($input, $errors) = validateDeleteDepartmentForm($dbConn);
    
      if (empty($errors)){
        // Update
        if (deleteDepartment($dbConn, $input)){
          $deleteSuccess = "<h3>You have successfully deleted the department. To manage another please click <a href='manage-department.php'>here</a>.</h3>";
        }

      } 

    } else{
      $input = array();
      $errors = array();
      $input['department_id'] = isset($_GET['department_id']) ? $_GET['department_id'] : null;

    } 

    if(isset($deleteSuccess)){
      echo $deleteSuccess;

    }

    if (isset($input, $errors) && !isset($deleteSuccess)){
      ?>
      <div class="col-sm-3"></div>
      <div class="col-sm-6">
        <h3 class="text-center">You are about to delete <?= $department_name; ?></h3>
        <form action="delete-department.php" name="deleteForm" method="POST">
          <div class="form-group">
            <input type="hidden" name="department_id" value="<?=$input['department_id'];?>">
          </div>
          <div class="update-error">
            <?php
            if (!empty($errors)) {
              foreach ($errors as $error) {
                echo "<p class='error'>$error</p>";

              }
            }
            ?>
          </div>
          <button type="submit" id="delete-button" class="btn btn-primary">DELETE</button>
        </form>
      </div>
      <div class="col-sm-3"></div>

      <?php
    }
      
    ?>
  </div>
</div>
<?php
getHTMLEnd();