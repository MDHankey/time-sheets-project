<?php
/* Delete Job
 * admin page
 * requires logged on
*/
require_once 'env/environment.php';
require_once 'functions/functions.php';
require_once 'functions/job-functions.php';
require_once 'class/PDODB.php';
session_start();

// Attempt to make connection to the database
$dbConn = PDODB::getConnection();

// Check for logged in user
$loggedIn = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// Page title
$pageTitle = 'Delete Job';  

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

// Check to see if a valid job id has been passed in
if(isset($_REQUEST['job_id'])){
    // Validation Checks
    if(is_numeric($_REQUEST['job_id'])){
        if(!empty(getJob($dbConn, $_REQUEST['job_id']))){
            $job_title = getJob($dbConn, $_REQUEST['job_id']);
        
        } else{
            $errorText = "You have not chosen a valid job to delete.  Please select a job <a href='manage-job.php'>here</a> to remove it from the system";
        
        }

    } else {
        $errorText = "You have not chosen a valid job to delete.  Please select an job <a href='manage-job.php'>here</a> to remove it from the system";
   
    }

} else{
    $errorText = "You have not chosen a valid job to delete.  Please select an job <a href='manage-job.php'>here</a> to remove it from the system";
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

    } else if (isset($_POST['job_id'])){
      // Validate
      list($input, $errors) = validateDeleteJobForm($dbConn);
    
      if (empty($errors)){
        // Update
        if (deleteJob($dbConn, $input)){
          $deleteSuccess = "<h3>You have successfully deleted the job. To manage another please click <a href='manage-job.php'>here</a>.</h3>";
        }

      } 

    } else{
      $input = array();
      $errors = array();
      $input['job_id'] = isset($_GET['job_id']) ? $_GET['job_id'] : null;

    } 

    if(isset($deleteSuccess)){
      echo $deleteSuccess;

    }

    if (isset($input, $errors) && !isset($deleteSuccess)){
      ?>
      <div class="col-sm-3"></div>
      <div class="col-sm-6">
        <h3 class="text-center">You are about to delete <?= $job_title; ?></h3>
        <form action="delete-job.php" name="deleteForm" method="POST">
          <div class="form-group">
            <input type="hidden" name="job_id" value="<?=$input['job_id'];?>">
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