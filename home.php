<?php
session_start();
$id = $_SESSION['ID'];
$admin = $_SESSION['Admin'];
$EmpPhys = $_SESSION['EmpPhys'];

$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$temp = mysqli_query($con, //This query can get the name of an employee or Physician.
"SELECT Name FROM
(Select Name FROM Employee WHERE Employee_ID=$id
UNION
Select Name FROM Physician WHERE Prac_ID=$id)
tt where Name is not null");
$name = mysqli_fetch_array($temp);
$name = $name["Name"];

if ($admin == 0)
  echo "You are not logged in as an admin.<br>";
else
  echo "You are loggied in as an admin.<br>";

echo "ID: " . $id ."<br>";

if ($EmpPhys == "E")
{ echo "Employee name: $name<br>";}
else
{ echo "Physician name: $name<br>";}

if ($EmpPhys == "E") //Physicians don't supervise/have a supervisor
{
  $temp = mysqli_query($con, "SELECT S.Name FROM Employee AS E, Employee AS S
  WHERE E.Employee_ID=$id AND E.Supervisor_ID=S.Employee_ID");

  if (0 == mysqli_num_rows($temp))
  {
    echo "You do not have a supervisor.<br>";
    $_SESSION['Supervisor'] = "";
  }
  else
  {
    $temp = mysqli_fetch_array($temp)["Name"];
    echo "Supervisor name: $temp<br>";
    $_SESSION['Supervisor'] = $temp;
  }
}

echo "<br><br><a href='editemployee.php'>View Profile</a>";

echo "<h3>Upcoming Meetings</h3>";

$temp = mysqli_query($con,
"SELECT M.DateTime AS 'Date', M.DurationMinutes AS 'Length', M.Location AS 'Location', H.Name as 'Head Name'
FROM Meeting AS M, Employee AS H
WHERE H.Employee_ID=M.Head_ID AND M.Meeting_ID IN
(SELECT Meeting_ID FROM Attends AS A WHERE A.Employee_ID=$id
UNION
SELECT Meeting_ID FROM Participates AS P WHERE P.Prac_ID=$id)");


if (0 == mysqli_num_rows($temp))
{echo "No upcoming meetings.";}
else
{
  echo "<table border='1'>
  <tr>
  <th>Date</th>
  <th>Length (Minutes)</th>
  <th>Location</th>
  <th>Meeting Head Name</th>
  </tr>";
  while ($row = mysqli_fetch_array($temp))
  {
    echo "<tr> <td> " . $row['Date'] . "</td> <td> " . $row['Length'] . "</td>";
    echo "<td> ". $row['Location'] . "</td> <td> " . $row['Head Name'] . "</td> ";
  }
  echo "</table> <br>";
}

if ($EmpPhys == "E")
{
  $temp = mysqli_query($con, "SELECT P.Name, Type, ProcedureDateTime as 'Date Time'
  FROM MedProcedure AS M, INVOLVED_IN as I, Patient AS P, Visit AS V
  WHERE I.Procedure_ID=M.Procedure_ID AND I.Employee_ID=$id AND M.Visit_ID=V.Visit_ID
  AND P.PHN = V.PatientPHN AND V.End_Date IS NULL");
}
else
{
  $temp = mysqli_query($con, "SELECT P.Name, Type, ProcedureDateTime as 'Date Time'
  FROM MedProcedure AS M, Patient AS P, Visit AS V
  WHERE M.RequestingPhysician_ID=$id AND M.Visit_ID=V.Visit_ID
  AND P.PHN = V.PatientPHN AND V.End_Date IS NULL");
}

if (mysqli_num_rows($temp) > 0)
{
  echo "<h3>Upcoming Procedures</h3>
  <table border='1'>
  <tr>
  <th>Patient Name</th>
  <th>Procedure Type</th>
  <th>Date and Time</th>
  </tr>";

  while ($row = mysqli_fetch_array($temp))
  { echo "<tr> <td> " . $row['Name'] . "</td> <td> " . $row['Type'] . "</td> <td> " . $row['Date Time'] . "</td>";}
  echo "</table> <br>";
}

$temp = mysqli_query($con,
"SELECT S.Name, S.Schedule, D.Name as 'Department'FROM Employee AS S, DEPARTMENT AS D
WHERE S.Supervisor_ID=$id AND S.Dept_ID=D.Department_ID
");

if (mysqli_num_rows($temp) > 0)
{
  echo "<h3>Your Supervisees</h3>
  <table border='1'>
  <tr>
  <th>Name</th>
  <th>Department</th>
  <th>Schedule</th>
  </tr>";

  while ($row = mysqli_fetch_array($temp))
  { echo "<tr> <td> " . $row['Name'] . "</td> <td> " . $row['Department'] . "</td> <td> " . $row['Schedule'] . "</td>"; }
  echo "</table> <br>";

}

if ($EmpPhys == "P")
{
  //Patient Name, Start Date, PHN
  $temp = mysqli_query($con, "SELECT P.name, V.Start_Date, V.PatientPHN, V.Visit_ID as 'Vis'
  FROM Visit AS V, Patient AS P
  WHERE P.PHN=V.PatientPHN AND V.AttendingPhysician_ID=$id AND V.End_Date IS NULL");
  if (mysqli_num_rows($temp) > 0)
  {
    echo "<h3>Your Active Visits:</h3>
    <table border='1'>
    <tr>
    <th>Patient Name</th>
    <th>Visit Start Date</th>
    <th>Patient PHN</th>
    <th>More Info</th></tr>";

    while ($row = mysqli_fetch_array($temp))
    {
      /*
      <form action='editemployee.php' method='post'>
        Change Home Phone Number: <input type='text' name='HPhone'> <input type='submit' value='Change Home Phone Number' name='Change_HPhone'>
      </form>

      */
      $temporary = "<form action='visitinfo.php' method='post'>";
      $temporary = $temporary . "<button type='submit' name='vis' value='" . $row['Vis'] . "'>Click Here</button></form>";

      echo "<tr> <td> " . $row['name'] . "</td> <td> " . $row['Start_Date'] . "</td> <td> " . $row['PatientPHN'] . "</td> <td> " . $temporary;
    }
    echo "</table> <br>";
  }

}

echo "<h3>Registration Links</h3>";
echo "<a href=Create/Patient.php> Register new Patient</a><br>";
if ($admin != 0)
{
  foreach(
    [["Employee", "Register new Employee"],
    ["Physician", "Register new Physician"],
    ["Department", "Register new Department"],
    ["Meeting", "Plan a meeting"],
    ["RoomBed", "Manage Rooms and Beds"]] as $v)
  {
    echo "<a href=Create/" . $v[0] . ".php>" . $v[1] . "</a><br>";
  }
  echo "<a href=managesupervision.php> Manage Supervision</a><br>";
  echo "<a href=manageadmin.php> Manage Admins</a><br>";
}

echo "<h3>Directory links</h3>";
$arr = array("Employees", "Departments", "Rooms", "Patients");
foreach ($arr as $v)
{
  echo "<a href=Directory/$v.php>$v</a><br>";
}

mysqli_close($con);
?>

<html><br><br><br>
<form action="index.php" method="post">
  <input type="submit" name="Logout" value="Logout"/>
</form>
</html>
