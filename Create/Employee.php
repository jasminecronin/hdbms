<html>
<body>

<h3>Add New Employee</h3>

<form action="Employee.php" method="post">
   Employee ID: <input type="number" name="Employee_ID"><br>
   Name: <input type="text" name="Name"><br>
   Office Phone Number: <input type="tel" name="officePhone"><br>
   Address: <input type="text" name="address"><br>
   Home Phone Number: <input type="tel" name="homePhone"><br>
   Email: <input type="email" name="email"><br>
   Date of Birth: <input type="date" name="Birth"><br>
   Status: <input type="text" name="Status"><br>
   Pay: <input type="number" name="Pay"><br>
   Social Insurance Number: <input type="number" name="SINNum"><br>
   Supervisor ID: <input type="number" name="Supervisor_ID"><br>
   Department ID: <input type="number" name="Dept_ID"><br>
   Schedule: <input type="text" name="Schedule"><br>
   <input type="submit" value="add" name="add">
</form>

</body>
</html>

<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "471");

if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if (isset($_POST['add'])) {

	$Employee_ID = $_POST['Employee_ID'];
	$Name = $_POST['Name'];
	$officePhone = $_POST['officePhone'];
	$address = $_POST['address'];
	$homePhone = $_POST['homePhone'];
	$email = $_POST['email'];
	$Birth = $_POST['Birth'];
	$Status = $_POST['Status'];
	$Pay = $_POST['Pay'];
	$SINNum = $_POST['SINNum'];
	$Supervisor_ID = $_POST['Supervisor_ID'];
	$Dept_ID = $_POST['Dept_ID'];
	$Schedule = $_POST['Schedule'];

	$sql = "INSERT INTO Employee (Employee_ID, Name, officePhone, address, homePhone, email, Birth, Status, Pay, SINNum, Supervisor_ID, Dept_ID, Schedule) VALUES ('$Employee_ID', '$Name', '$officePhone', '$address', '$homePhone', '$email', '$Birth', '$Status', '$Pay', '$SINNum', '$Supervisor_ID', '$Dept_ID', '$Schedule')";
	if(!mysqli_query($con,$sql)) {
		echo 'Data not inserted';
	} else {
		echo "1 record added";
	}
 }

echo "</table> <br> <a href=../home.php>Return to previous page</a><br>";

mysqli_close($con);

?>
