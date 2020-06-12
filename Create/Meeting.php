<html>
<body>

<h3>Schedule a Meeting</h3>

<form action="Meeting.php" method="post">
   Meeting ID: <input type="number" name="Meeting_ID"><br>
   Meeting Head ID: <input type="number" name="Head_ID"><br>
   Date and Time: <input type="datetime-local" name="DateTime"><br>
   Meeting Duration (minutes): <input type="number" name="DurationMinutes"><br>
   Location: <input type="text" name="Location"><br>
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

	$Meeting_ID = $_POST['Meeting_ID'];
	$Head_ID = $_POST['Head_ID'];
	$DateTime = $_POST['DateTime'];
	$DurationMinutes = $_POST['DurationMinutes'];
	$Location = $_POST['Location'];
	$Employee_ID = $_POST['Head_ID'];

	// Need to add meeting requester into the attends/participates relationship

	$sql = "INSERT INTO Meeting (Meeting_ID, Head_ID, DateTime, DurationMinutes, Location) VALUES ('$Meeting_ID','$Head_ID','$DateTime', '$DurationMinutes', '$Location')";
	if(!mysqli_query($con,$sql)) {
		echo 'Data not inserted\n';
		die('Error: ' . mysqli_error($con));
	} else {
		echo "1 meeting added\n";
	}

	$sql = "INSERT INTO Attends (Employee_ID, Meeting_ID) VALUES ('$Employee_ID', '$Meeting_ID')";
	if(!mysqli_query($con,$sql)) {
		echo 'Data not inserted\n';
		die('Error: ' . mysqli_error($con));
	} else {
		echo "Meeting head attendance requested\n";
	}
 }

echo "</table> <br> <a href=../home.php>Return to previous page</a><br>";

mysqli_close($con);

?>
