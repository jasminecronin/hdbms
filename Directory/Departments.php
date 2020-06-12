<?php
$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{ echo "Failed to connect to MySQL: " . mysqli_connect_error();}

echo "<table border='1'>
<tr>
<th>Name</th>
<th>ID</th>
<th>Manager Name</th>
</tr>";


$result = mysqli_query($con,"SELECT D.Name as Name, D.Department_ID as ID, M.Name as 'Manager Name'
FROM Department AS D, Employee as M
WHERE D.Manager_ID=M.Employee_ID");
while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['Name'] . "</td>";
  echo "<td>" . $row['ID'] . "</td>";
  echo "<td>" . $row['Manager Name'] . "</td>";
}
echo "</table> <br>";
echo "<a href=../home.php>Return to previous page</a><br>";
mysqli_close($con);
?>
