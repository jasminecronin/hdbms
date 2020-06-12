<?php
$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{ echo "Failed to connect to MySQL: " . mysqli_connect_error();}

echo "<h2>List of Rooms</h2>";
echo "<table border='1'>
<tr>
<th>Room Number</th>
<th>Department</th>
</tr>";


$result = mysqli_query($con,"SELECT R.Room_Number as 'Room Number', D.Name as Department
FROM Room as R, Department as D
WHERE R.Dept_ID=D.Department_ID");

while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['Room Number'] . "</td>";
  echo "<td>" . $row['Department'] . "</td>";
}
echo "</table> <br>";

echo "<h2>List of Beds</h2>
<table border='1'>
<tr>
<th>Department Name</th>
<th>Room Number</th>
<th>Bed Number</th>
<th>Needs Cleaning</th>
</tr>";
$result = mysqli_query($con, "SELECT D.Name, B.Room_Number, B.Bed_Number, B.Needs_Cleaning
  FROM Bed as B, Department as D WHERE B.Dept_ID=D.Department_ID ORDER BY Name ASC, Room_Number ASC, Bed_Number ASC");
while ($row = mysqli_fetch_array($result))
{
  if ($row['Needs_Cleaning'] == "0")
  {
    $row['Needs_Cleaning'] = "No";
  }
  else
  {
    $row['Needs_Cleaning'] = "Yes";
  }

  echo "
  <tr>
  <td> " . $row['Name'] . "
  <td> " . $row['Room_Number'] . "
  <td> " . $row ['Bed_Number'] . "
  <td> " . $row['Needs_Cleaning'] . "
  </tr>
  ";
}

echo "</table> <br>
<a href=../home.php>Return to previous page</a><br>";
mysqli_close($con);
?>
