<html>
<body>

<h3>Register a New Physician</h3>

<form action="Physician.php" method="post">
   Full Name: <input type="text" name="Name"><br>
   Practitioner ID: <input type="number" name="Prac_ID"><br>
   Physician Status: <input type="text" name="Status"><br>
   GP or Specialist: <input type="text" name="GPSpecialistStatus"><br>
   Family GP or General GP (if applicable): <input type="text" name="FamilyGeneralStatus"><br>
   Specialty (f applicable): <input type="text" name="Specialty"><br>
   Practice Interest: <input type="text" name="Practice_Interest"><br>
   Address: <input type="text" name="Address"><br>
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

	$Name = $_POST['Name'];
	$Prac_ID = $_POST['Prac_ID'];
	$Status = $_POST['Status'];
	$GPSpecialistStatus = $_POST['GPSpecialistStatus'];
	$FamilyGeneralStatus = $_POST['FamilyGeneralStatus'];
	$Specialty = $_POST['Specialty'];
	$Practice_Interest = $_POST['Practice_Interest'];
	$Address = $_POST['Address'];

	// Need to add meeting requester into the attends/participates relationship

	$sql = "INSERT INTO Physician (Name, Prac_ID, Status, GPSpecialistStatus, FamilyGeneralStatus, Specialty, Practice_Interest, Address) VALUES ('$Name', '$Prac_ID', '$Status', '$GPSpecialistStatus', '$FamilyGeneralStatus', '$Specialty', '$Practice_Interest', '$Address')";
	if(!mysqli_query($con,$sql)) {
		echo 'Data not inserted\n';
		die('Error: ' . mysqli_error($con));
	} else {
		echo "1 record added\n";
	}
 }

echo "</table> <br> <a href=../home.php>Return to previous page</a><br>";

mysqli_close($con);

?>
