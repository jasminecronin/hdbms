<?php
session_start();
$phn = $_POST['PHN'];

$con = mysqli_connect("localhost", "root", "", "471");

if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT * FROM Patient WHERE PHN=$phn");
$result = mysqli_fetch_array($result);

foreach([["Name", $result['name']],
["PHN", $result['PHN']],
["DOB", $result['Birth']],
["Address", $result['address']],
["Height", $result['height']],
["Weight", $result['weight']],
["Phone Number", $result['phone']]] as $v)
{
  echo "<strong>" . $v[0] . "</strong>: " . $v[1] . "<br>";
}

$result = mysqli_query($con, "SELECT V.Visit_ID, V.Start_Date, IFNULL(V.End_Date, 'Ongoing') AS 'End_Date', A.Name
FROM Visit as V, Physician AS A
WHERE PatientPHN=$phn AND A.Prac_ID=V.AttendingPhysician_ID");

echo "<br><br><strong>To edit patient information, click the \"Full Visit Info\" link under any active visit.</strong>";

if (0 == mysqli_num_rows($result))
{
  echo "<strong>Patient has never had a visit.</strong>";
}
else
{
  echo "<table border='1'>
  <tr>
  <th>Visit ID</th>
  <th>Attending Physician Name</th>
  <th>Visit Start Date</th>
  <th>Visit End Date</th>
  <th>Full Visit Info</th>
  </tr>";

  while ($row = mysqli_fetch_array($result))
  {
    echo "<tr>";
    echo "<td>" . $row['Visit_ID'] . "</td>";
    echo "<td>" . $row['Name'] . "</td>";
    echo "<td>" . $row['Start_Date'] . "</td>";
    echo "<td>" . $row['End_Date'] . "</td>";
    echo "<td><form action='visitinfo.php' method='post'>
    <button type='submit' name='vis' value='" . $row['Visit_ID'] . "'>Click Here</button></form></td>";
  }
  echo "</table><br>";
}
echo "<br> <a href=Directory/Patients.php>Return to patient directory</a>";
echo "</table> <br> <a href=home.php>Return to home page</a><br>";

?>
