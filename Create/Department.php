<html>
<body>

<h3>Add New Department</h3>

<form action="Department.php" method="post">
   Department ID: <input type="number" name="Department_ID"><br>
   Name: <input type="text" name="Name"><br>
   Manager ID: <input type="number" name="Manager_ID"><br>
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

	$Department_ID = $_POST['Department_ID'];
	$Name = $_POST['Name'];
	$Manager_ID = $_POST['Manager_ID'];

	$sql = "INSERT INTO Department (Department_ID, Name, Manager_ID) VALUES ('$Department_ID','$Name','$Manager_ID')";
	if(!mysqli_query($con,$sql)) {
		echo 'Data not inserted';
	} else {
		echo "1 record added";
	}
 }

echo "</table> <br> <a href=../home.php>Return to previous page</a><br>";

mysqli_close($con);

?>
