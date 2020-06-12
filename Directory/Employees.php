<?php
$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{ echo "Failed to connect to MySQL: " . mysqli_connect_error();}

echo "<h2>Hospital Employees: </h2>";

echo "<table border='1'>
<tr>
<th>Name</th>
<th>Office Phone</th>
<th>Email</th>
<th>Department</th>
<th>Schedule</th>
<th>Admin Status</th>
</tr>";

$result = mysqli_query($con,"SELECT E.Name, E.officePhone as Phone, E.email, D.Name as Department, E.Schedule, E.Admin
FROM Employee as E, Department as D
WHERE E.Dept_ID=D.Department_ID");

while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['Name'] . "</td>";
  echo "<td>" . $row['Phone'] . "</td>";
  echo "<td>" . $row['email'] . "</td>";
  echo "<td>" . $row['Department'] . "</td>";
  echo "<td>" . $row['Schedule'] . "</td>";
  if ($row['Admin'] == "0")
  { $row['Admin'] = "Not Admin";}
  else
  { $row['Admin'] = "Admin";}
  echo "<td>" . $row['Admin'] . "</td>";
}
echo "</table> <br>";

echo "<h2>Hospital Physicians:</h2>";

echo "<table border='1'>
<tr>
<th>Name</th>
<th>GP / Specialist Status</th>
<th>GP Type</th>
<th>Specialty</th>
<th>Practice Interest</th>
</tr>";

$result = mysqli_query($con, "SELECT Name, GPSpecialistStatus, FamilyGeneralStatus, Specialty, Practice_Interest FROM Physician");
while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['Name'] . "</td>";
  echo "<td>" . $row['GPSpecialistStatus'] . "</td>";
  echo "<td>" . $row['FamilyGeneralStatus'] . "</td>";
  echo "<td>" . $row['Specialty'] . "</td>";
  echo "<td>" . $row['Practice_Interest'] . "</td>";
}

echo "</table> <br> <a href=../home.php>Return to previous page</a><br>";
mysqli_close($con);
?>
