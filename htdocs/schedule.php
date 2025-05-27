<!doctype html>

<?php
session_start();
include ("../include/db.php");
include ("../include/functions.php");

$userLogged = $_SESSION['user_email'];

$results=getUser($con,$userLogged);
?>
<?php

if($_SESSION['user_email']==true){
}
else{
  header('location: login.php');
}


include ("functions/functions.php");
?>


<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Fitzone Gym | Schedule Training</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    

    <!-- Bootstrap core CSS -->
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
	
	<style> 
		label{margin-left: 20px;} 
		#datepicker{width:180px;} 
		#datepicker > span:hover{cursor: pointer;} 
	</style>

    
    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
  </head>
  <body>
    
  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">

<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Fitzone Gym</a>
<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>
<input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
<div class="navbar-nav">
  <div class="nav-item text-nowrap">
    <a class="nav-link px-3" href="logout.php">Sign out</a>
  </div>
</div>    

</header>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="Home.php">
              <span data-feather="home"></span>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Home.php?view_users">
              <span data-feather="calendar"></span>
              Days
            </a>
          </li>
	          <ul id="days">
					<?php  
						getDays();
					?>
				</ul>
      

          <li class="nav-item">
            <a class="nav-link" href="Home.php?view_trainers">
              <span data-feather="users"></span>
              View Trainers
            </a>
              
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Home.php?view_exercises">
              <span data-feather="bar-chart-2"></span>
              View Exercise
            </a>
            <ul id="days">
					<?php 
						getExercise();
					?>
				</ul>

          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="layers"></span>
              Integrations
            </a>
          </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Saved reports</span>
          <a class="link-secondary" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
        
        <ul class="nav flex-column mb-2">
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Current month
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Last quarter
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Social engagement
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">
              <span data-feather="file-text"></span>
              Year-end sale
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Welcome <strong><?php echo  $results['user_name']?></strong> your package is <B><?php echo  $results['package']; ?> </B> - -Schedule your training session</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary"><?php echo  $_SESSION['user_email']?></button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>
            This week
          </button>
        </div>
      </div>

     <!-- <canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>-->

      <h2></h2>
      <div class="table-responsive">
        <!-- Table -->


     
			</div>


		 <div class="container"> 
      
		
			<form method="post" action="" enctype="multipart/form-data">
		<table width="794px" align="center" border="1" bgcolor="whitesmoke">
			<tr>
				<td colspan="2" align="center"><h1></h1></td>
			</tr>
			<tr>
				<td align="right"><b>Select Trainer : </b></td>
				<td>
					<select class="form-select" name="user">
						<option></option>
						<?php
						$get_user="SELECT * FROM trainer";
						$run_user=mysqli_query($con, $get_user);
						while($row_days=mysqli_fetch_array($run_user)){
							$tran_id=$row_days['tran_id'];
							$tran_name=$row_days['tran_name'];
							echo "<option value='$tran_id'>$tran_name</option>";
						}
					?>
					</select>
				</td>
			</tr>
			
			
			
		
			
					<tr>
				<td align="right"><b>Training date :</b></td>
				<td>

					<input type="date" id="birthday" class="form-select" name="gymdate">
					  
					  
				</td>
			</tr>
			
					<tr>
				<td align="right"><b>Start time :</b></td>
				<td>

					<input type="time" id="appt" class="form-select" name="stime">
					  
					  
				</td>
			</tr>
			
			
		
			<tr>
			<br>
				<td colspan="2" align="center"><input type="submit" class="btn btn-info" name="schedule" value="Schedule"></td>
			</tr>
		</table>
	</form>
    </div> 
			
	
	 
			  
	 <?php
					
				$dbDate = strtotime( $results['Date']);
				$ema = $results['Trainer'];
				
				
					
					$trainerSelected = getTrainer($con,$ema);
					
					if($trainerSelected != '')
					{
						$name = " "." Your Trainer Is " .$trainerSelected['tran_name'];
					}
					else{
						$name = '';
					}
					
					$current_datetime = date('Y-m-d');
					$send_date = date("Y-m-d", strtotime($results['Date'])); // suppose $row['send_date']'s value is '2016-10-17 15:00'

					if(strtotime($current_datetime) <= strtotime($send_date)){?>
					
					
					
					<div class="container m-3 p-3">
							 <h4>Training scheduled</h4>
							
							 <div class="d-print-none bg-warning p-3">You have a training session at <?php echo $results['Date']. " ". $results['time'] . $name ?></div>

				     </div>
						
						
						
						
					<?php	
					}
					else{?>
					
					
					
					<div class="container m-3 p-3">
							 <h4>Training scheduled</h4>
							
							 <div class="d-print-none bg-warning p-3">No training schechuled....... </div>

				     </div>
						
						
						
						
					<?php	
					}
			
				

				?> 
	

   

		</div>
    <div class="container">
    <div class="main-body">
    
          <!-- Breadcrumb 
          <nav aria-label="breadcrumb" class="main-breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="index.html">Home</a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0)">User</a></li>
              <li class="breadcrumb-item active" aria-current="page">User Profile</li>
            </ol>
          </nav>
           Breadcrumb -->
            

           
    
	 
	 


       

        </div>
    </div>

      </div>
    </main>
  </div>
</div>

<?php 
			
	       if(isset($_POST['schedule']))
			   {
				 $email = $results['user_email'];
				 $user_ = $_POST['user'];
				 $gymdate = $_POST['gymdate'];
				 $stime = $_POST['stime'];
				 
				 
				 $send_dateValidate = date("Y-m-d", strtotime($gymdate));
				 
				 
						//Validations
					if($gymdate==''){
						echo "<script>alert('Please Fill All The Fields!')</script>";
						exit();
					}
					elseif ( $stime=='') {
					echo "<script>alert('Please Fill All The Fields!')</script>";
					exit();
					}
					
					elseif (strtotime($current_datetime) >  strtotime($send_dateValidate)){
					echo "<script>alert('Selected date can not be less than today!')</script>";
					exit();
					}
				

					else{
						
						$insert_exer = "UPDATE users SET Trainser = '$user_',Date = '$gymdate',time = '$stime'  WHERE user_email = '$email'";
						$run_exer = mysqli_query($con, $insert_exer);
				 
						 if ($run_exer) {
						echo "<script>alert('Training date scheduled')</script>";
						echo "<script>window.open('schedule.php','_self')</script>";
							}
						else{
						echo "<script>alert('Error')</script>";
					}

						
			   }
			   }
			   
			   


?>



                <!-- Page level plugins -->
                <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

      <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>


      <script src= 
"https://code.jquery.com/jquery-3.6.1.min.js"
		integrity= 
"sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
		crossorigin="anonymous"> 
	</script> 
	
	<script> 
		$(function () { 
			$("#datepicker").datepicker({ 
				autoclose: true, 
				todayHighlight: true, 
				todayBtn : "linked", 
				title : "Geeksforgeeks datepicker" 
			}).datepicker('update', new Date()); 
		}); 
	</script> 
	<script src= 
"https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
		integrity= 
"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
		crossorigin="anonymous"> 
	</script> 
	<script src= 
"https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
		integrity= 
"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
		crossorigin="anonymous"> 
	</script> 
	<script src= 
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"> 
	</script> 
  </body>
</html>
