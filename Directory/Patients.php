<?php
$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{ echo "Failed to connect to MySQL: " . mysqli_connect_error();}

echo "<table border='1'>
<tr>
<th>PHN</th>
<th>Name</th>
<th>DOB</th>
<th>Visit History</th>
</tr>";


$result = mysqli_query($con,"SELECT PHN, name as Name, birth as DOB
FROM Patient");

while ($row = mysqli_fetch_array($result))
{
  echo "<tr>";
  echo "<td>" . $row['PHN'] . "</td>";
  echo "<td>" . $row['Name'] . "</td>";
  echo "<td>" . $row['DOB'] . "</td>";
  echo "<td><form action='../visits.php' method='post'>
  <button type='submit' name='PHN' value='" . $row['PHN'] . "'>Click Here</button></form></td>";
}
echo "</table> <br>";
echo "<a href=../home.php>Return to previous page</a><br>";
mysqli_close($con);
?>
