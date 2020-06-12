<html>
<body>

<h3>Add New Patient</h3>

<form action="Patient.php" method="post">
   PHN: <input type="number" name="PHN"><br>
   Name: <input type="text" name="name"><br>
   Date of Birth: <input type="date" name="Birth"><br>
   Address: <input type="text" name="address"><br>
   Height: <input type="number" name="height"><br>
   Weight: <input type="number" name="weight"><br>
   Phone Number: <input type="tel" name="phone"><br>
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

	$PHN = $_POST['PHN'];
	$name = $_POST['name'];
	$Birth = $_POST['Birth'];
	$address = $_POST['address'];
	$height = $_POST['height'];
	$weight = $_POST['weight'];
	$phone = $_POST['phone'];

	$sql = "INSERT INTO Patient (PHN, name, Birth, address, height, weight, phone) VALUES ('$PHN','$name','$Birth','$address','$height','$weight','$phone')";
	if(!mysqli_query($con,$sql)) {
		echo 'Data not inserted';
	} else {
		echo "1 record added";
	}
 }

echo "</table> <br> <a href=../home.php>Return to previous page</a><br>";

mysqli_close($con);

?>
