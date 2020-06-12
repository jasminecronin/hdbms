<?php
$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{ echo "Failed to connect to MySQL: " . mysqli_connect_error();}

if (isset($_POST['EmpID']))
{
  $id = $_POST['EmpID'];
  $result = mysqli_query($con, "UPDATE Employee SET Admin=NOT Admin WHERE Employee_ID=$id");
}

echo "<h2>Employees who are Admins:</h2>";

echo "<table border='1'>
<tr>
<th>Name</th>
<th>Office Phone</th>
<th>Email</th>
<th>Department</th>
<th>Change Admin Status</th>
</tr>";

$result = mysqli_query($con,"SELECT E.Employee_ID, E.Name, E.officePhone as Phone, E.email, D.Name as Department, E.Admin
FROM Employee as E, Department as D WHERE E.Dept_ID=D.Department_ID AND E.Admin");

while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['Name'] . "</td>";
  echo "<td>" . $row['Phone'] . "</td>";
  echo "<td>" . $row['email'] . "</td>";
  echo "<td>" . $row['Department'] . "</td>";
  echo "<td>
  <form action='manageadmin.php' method='post'>
  <button type='submit' name='EmpID' value='" . $row['Employee_ID'] . "'>Revoke Admin Status</button></form>
  </td>";
}
echo "</table> <br>";

echo "<h2>Employees who are not Admins:</h2>";

echo "<table border='1'>
<tr>
<th>Name</th>
<th>Office Phone</th>
<th>Email</th>
<th>Department</th>
<th>Change Admin Status</th>
</tr>";

$result = mysqli_query($con,"SELECT E.Employee_ID, E.Name, E.officePhone as Phone, E.email, D.Name as Department, E.Admin
FROM Employee as E, Department as D WHERE E.Dept_ID=D.Department_ID AND NOT E.Admin");

while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['Name'] . "</td>";
  echo "<td>" . $row['Phone'] . "</td>";
  echo "<td>" . $row['email'] . "</td>";
  echo "<td>" . $row['Department'] . "</td>";
  echo "<td>
  <form action='manageadmin.php' method='post'>
  <button type='submit' name='EmpID' value='" . $row['Employee_ID'] . "'> Assign Admin Status</button></form>
  </td>";
}
echo "</table> <br>";
echo "<a href=../home.php>Return to previous page</a><br>";
mysqli_close($con);
?>
