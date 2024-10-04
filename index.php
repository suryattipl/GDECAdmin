<?php
require_once "header.php";
if (isset($_SESSION['admin_name'])) {
    echo "<script>window.location='dashboard.php';</script>";
}

if (isset($_POST['admin-login-submit']) && $_POST['admin-login-submit'] == "Login") {
    
    $admin_email    = trim($_POST['admin_email']);
    $admin_password = trim($_POST['admin_password']);
    
    $adminQuery     = "SELECT * FROM admin WHERE admin_email='".$admin_email."'";
    $results        =  mysqli_query($dbhandle,$adminQuery);
    $adminDetails   =  mysqli_fetch_assoc($results);
    //echo "<pre>";print_r($adminDetails);die;
    if(isset($adminDetails) && !empty($adminDetails['admin_email'])):
        if($admin_password == $adminDetails['password']):
            $_SESSION['admin_name']         =   $adminDetails['admin_name'];
            $_SESSION['admin_email']        =   $adminDetails['admin_email'];
            $_SESSION['admin_phone']        =   $adminDetails['admin_phone'];
            $_SESSION['admin_gender']       =   $adminDetails['admin_gender'];
            $_SESSION['admin_type']         =   $adminDetails['admin_type'];
            $_SESSION['admin_id']           =   $adminDetails['id'];
            $_SESSION['division']           =   $adminDetails['division'];
            echo "<script>window.location='dashboard.php';</script>";
            unset($_POST);
        else:
            echo "<span style='text-align:center;margin-top:10px;display: block;' class='required'>Password is wrong.Please try again.<span>"; // wrong details
        endif;
    else:
        echo "<span style='text-align:center;margin-top:10px;display: block;' class='required'>Either Username OR Password is wrong.Please try again.<span>"; // wrong details
        unset($_POST);
    endif;
}
?>
<div class="container index">
       <div class="container containerfff">
          <div class="row">
             <div class="col-md-12">
                <div class="panel">
                   <div class="panel-body">
                      <div class="login-page">
                         <?php
                            if(isset($_GET['action']) AND $_GET['action']=="fail") {
                                echo "Login Failed";
                            } 
                            ?>
                         <div class="form form-log">
                            <p class="member">Admin Login</p>
                            <div class="message"></div>
                            <br>
                            <form class="login-form" id="login-form" method="post" novalidate="novalidate" autocomplete="off">
                               <input type="email" id="email" name="admin_email" placeholder="Username" autocomplete="off" required="" aria-required="true">
                               <input type="password" id="password" name="admin_password" placeholder="**********" autocomplete="off" required="" aria-required="true">
                               <button type="submit" name="admin-login-submit" class="login" value="Login">Login</button>
                               
                            </form>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>