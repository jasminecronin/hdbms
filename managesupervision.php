<html>
<body>

  <h3>(Re)Assign Employee Supervisor</h3>
  <p>Use this to assign a supervisor to an employee. If the employee already has a supervisor, that will be changed.<br>
  To make this employee have no supervisor, enter -1 in the Supervisor ID field.</p>

  <form action="managesupervision.php" method="post">
    Employee ID: <input type="text" name="EmpID"><br>
    Supervisor ID: <input type="text" name="SupID"><br>
    <input type="submit" value="Assign Supervisor" name="Assign_Supervisor">
  </form>

  <h2>Batch Reassign Supervisor</h2>
  <p>Use this to reassign all supervisees of a given supervisor to another employee.<br>
  To make these employees have no supervisor, enter -1 in the New Supervisor ID field.</p>

  <form action="managesupervision.php" method="post">
    Current Supervisor ID: <input type="text" name="OldSupID"><br>
    New Supervisor ID: <input type="text" name="NewSupID"><br>
    <input type="submit" value="Batch Reassign Supervisor" name="Batch_Reassign">
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

if (isset($_POST['Batch_Reassign']))
{
  $OldSupID = $_POST['OldSupID'];
  $NewSupID = $_POST['NewSupID'];

  if (empty($OldSupID) or empty($NewSupID) or ($NewSupID == $OldSupID))
  { echo "<strong> Error: improper input. Inputs must be distinct and nonempty.</strong>";}
  else
  {
    $query = "";
    if ($NewSupID == "-1")
    {$query = "UPDATE Employee SET Supervisor_ID=NULL WHERE Supervisor_ID=$OldSupID";}
    else
    {$query = "UPDATE Employee SET Supervisor_ID=$NewSupID WHERE Supervisor_ID=$OldSupID AND
    $NewSupID IN (SELECT Employee_ID FROM (SELECT * FROM EMPLOYEE) AS V)";}

    $result = mysqli_query($con, $query);
    echo "<strong> Number of records updated: " . mysqli_affected_rows($con) . "</strong>";
  }
}

else if (isset($_POST['Assign_Supervisor']))
{
  $EmpID = $_POST['EmpID'];
  $SupID = $_POST['SupID'];
  if (empty($EmpID) or empty($SupID) or ($SupID == $EmpID))
  { echo "<strong> Error: improper input. Inputs must be distinct and nonempty.</strong>";}
  else
  {
    $query = "";
    if ($SupID=="-1")
    {$query = "UPDATE Employee SET Supervisor_ID=NULL WHERE Employee_ID=$EmpID";}
    else
    {$query = "UPDATE Employee SET Supervisor_ID=$SupID WHERE Employee_ID=$EmpID
    AND $SupID IN (SELECT Employee_ID FROM (SELECT * FROM EMPLOYEE) AS V)";}

    $result = mysqli_query($con, $query);

    if (1 == mysqli_affected_rows($con))
    { echo "<strong> Update successful; 1 row altered.</strong><br>"; }
    else
    { echo "<strong> Update unsuccessful. Number of rows altered = " . mysqli_affected_rows($con) . ".</strong><br>";}
  }
}

$result = mysqli_query($con,
"SELECT E.Name, D1.Name as 'Department', E.Email, E.Employee_ID as 'Employee ID',
S.Name as 'Supervisor Name', D2.Name as 'Supervisor Dept', S.Email as 'Supervisor Email', S.Employee_ID as 'Supervisor ID'
FROM Employee as E, Employee as S, Department as D1, Department as D2
WHERE E.Supervisor_ID=S.Employee_ID AND D1.Department_ID=E.Dept_ID AND D2.Department_ID=S.Dept_ID");

if (0 < mysqli_num_rows($result))
{
  echo "<h2>List of employees by supervisor:</h2>";

  echo "<table border='1'>
  <tr>
  <th>Name</th> <th>Department</th> <th>Email</th> <th>Employee ID</th>
  <th>Supervisor Name</th> <th>Supervisor Dept.</th> <th>Supervisor Email</th> <th>Supervisor ID</th></tr>";

  while ($row = mysqli_fetch_array($result))
  {
    echo "<tr>";
    echo "<td>" . $row['Name'] . "</td>";
    echo "<td>" . $row['Department'] . "</td>";
    echo "<td>" . $row['Email'] . "</td>";
    echo "<td>" . $row['Employee ID'] . "</td>";

    echo "<td>" . $row['Supervisor Name'] . "</td>";
    echo "<td>" . $row['Supervisor Dept'] . "</td>";
    echo "<td>" . $row['Supervisor Email'] . "</td>";
    echo "<td>" . $row['Supervisor ID'] . "</td></tr>";
  }

  echo "</table> <br>";
}
else
{ echo "There are no employees with a supervisor."; }

$result = mysqli_query($con,
"SELECT E.Name, D.name as 'Department', E.Email, E.Employee_ID
FROM Employee AS E, Department AS D
WHERE E.Dept_ID=D.Department_ID and NOT EXISTS
(SELECT * FROM Employee AS S WHERE E.Supervisor_ID=S.Employee_ID)");

if (mysqli_num_rows($result) > 0)
{
  echo "<h2>List of employees with no supervisor:</h2>";

  $result = mysqli_query($con,
  "SELECT E.Name, D.name as 'Department', E.Email, E.Employee_ID
  FROM Employee AS E, Department AS D
  WHERE E.Dept_ID=D.Department_ID and NOT EXISTS
  (SELECT * FROM Employee AS S WHERE E.Supervisor_ID=S.Employee_ID)");

  echo "<table border='1'>
  <tr> <th>Name</th> <th>Department</th> <th>Email</th> <th>ID</th></tr>";

  while ($row = mysqli_fetch_array($result))
  {
    echo "<tr>";
    echo "<td>" . $row['Name'] . "</td>";
    echo "<td>" . $row['Department'] . "</td>";
    echo "<td>" . $row['Email'] . "</td>";
    echo "<td>" . $row['Employee_ID'] . "</td></tr>";
  }
  echo "</table> <br>";
}
else
{
  echo "There are no employees with no supervisors.<br>";
}

echo "<br><a href=../home.php>Return to previous page</a><br>";
mysqli_close($con);

?>
