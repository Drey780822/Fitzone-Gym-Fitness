<?php


	//Select a user
		function getUser($con,$user_id)
				{
						$sql ="SELECT * FROM users WHERE user_email = '$user_id'";
						$query = mysqli_query($con,$sql) or die(mysqli_error($con));
						while($rows=mysqli_fetch_array($query))
						{
							return $rows;
						}
					
				}

	 function getTrainer($con,$id)
				{

					$select_user ="SELECT * FROM trainer WHERE tran_id='$id'";
					$query = mysqli_query($con,$select_user) or die(mysqli_error($con));
						while($rows=mysqli_fetch_array($query))
						{
							return $rows;
						}
				}


?>