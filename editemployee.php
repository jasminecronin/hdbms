<?php
session_start();
$id = $_SESSION['ID'];
$EmpPhys = $_SESSION['EmpPhys'];

function quotes($in) { return "\"" . $in . "\""; }

$con = mysqli_connect("localhost", "root", "", "471");

if (mysqli_connect_errno($con)) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }

if ($EmpPhys == "E")
{
  //v[0] = what to check for in post
  //v[1] = the trait name (in the sql query)
  //v[3] = the new value for that trait
  foreach([["Change_Address", "address", "address"],
  ["Change_Email", "email", "email"],
  ["Change_HPhone", "homePhone", "HPhone"],
  ["Change_OPhone", "officePhone", "OPhone"],
  ["Change_Schedule", "Schedule", "schedule"],
  ["Change_Password", "Password", "pass"]] as $v)
  {
    if (isset($_POST[$v[0]]))
    {
      $trait_name = $v[1];
      $new_val = $_POST[$v[2]];

      if (empty($new_val)) { echo "<strong>Error: New value should not be blank</strong><br>";}
      else
      {
        $new_val = quotes($new_val);
        $result = mysqli_query($con, "UPDATE Employee SET $trait_name=$new_val WHERE Employee_ID=$id");
        echo "<strong> Request recieved. Number of records updated: " . mysqli_affected_rows($con) . "</strong>";
      }
    }
  }
}
else
{
  foreach([["Change_Address", "Address", "address"],
  ["Change_PracInt", "Practice_Interest", "PracInt"],
  ["Change_Status", "Status", "status"],
  ["Change_GPSpecial", "GPSpecialistStatus", "GPSpecial"],
  ["Change_FamGen", "FamilyGeneralStatus", "FamGen"],
  ["Change_Specialty", "Specialty", "specialty"],
  ["Change_Password", "Password", "pass"]] as $v)
  {
    if (isset($_POST[$v[0]]))
    {
      $trait_name = $v[1];
      $new_val = $_POST[$v[2]];

      if (empty($new_val)) { echo "<strong>Error: New value should not be blank</strong><br>";}
      else
      {
        $new_val = quotes($new_val);
        $result = mysqli_query($con, "UPDATE Physician SET $trait_name=$new_val WHERE Prac_ID=$id");
        echo "<strong> Request recieved. Number of records updated: " . mysqli_affected_rows($con) . "</strong>";
      }
    }
  }
}

echo "<h2>Employee Profile</h2>";

if ($EmpPhys == "E")
{
  $result = mysqli_query($con, "SELECT E.*, D.Name as 'DName' FROM Employee AS E, Department AS D
  WHERE E.Employee_ID=$id AND E.Dept_ID=D.Department_ID");
  $result = mysqli_fetch_array($result);

  foreach([["Employee ID", $id],
  ["Name", $result["Name"]],
  ["Email", $result["email"]],
  ["Office Phone Number", $result['officePhone']],
  ["Home Phone Number", $result['homePhone']],
  ["Address", $result['address']],
  ["Date of Birth", $result['Birth']],
  ["Salary", "$" . $result['Pay']],
  ["SIN Number", $result['SINNum']],
  ["Department Name", $result['DName']],
  ["Status", $result['Status']],
  ["Schedule", $result['Schedule']]] as $v)
  {
    echo "<strong>" . $v[0] . "</strong>: " . $v[1] . "<br>";
  }
}

else
{
  $result = mysqli_query($con, "SELECT * FROM Physician WHERE $id=Prac_ID");
  $result = mysqli_fetch_array($result);

  foreach([["Physician ID", $id],
  ["Name", $result["Name"]],
  ["Status", $result["Status"]],
  ["Practice Interest", $result["Practice_Interest"]],
  ["Address", $result["Address"]],
  ["GP/Specialist", $result["GPSpecialistStatus"]],
  ["Family/General Status", $result["FamilyGeneralStatus"]],
  ["Specialty", $result["Specialty"]]] as $v)
  {
    echo "<strong>" . $v[0] . "</strong>: " . $v[1] . "<br>";
  }
}

echo "
<html>
<body><h3>Modify Profile</h3>
<form action='editemployee.php' method='post'>
  Change Address: <input type='text' name='address'> <input type='submit' value='Change Address' name='Change_Address'>
</form>
";

if ($EmpPhys == "E")
{
    echo "<form action='editemployee.php' method='post'>
        Change Email Address: <input type='text' name='email'> <input type='submit' value='Change Email' name='Change_Email'>
      </form>


      <form action='editemployee.php' method='post'>
        Change Home Phone Number: <input type='text' name='HPhone'> <input type='submit' value='Change Home Phone Number' name='Change_HPhone'>
      </form>

      <form action='editemployee.php' method='post'>
        Change Office Phone Number: <input type='text' name='OPhone'> <input type='submit' value='Change Office Phone Number' name='Change_OPhone'>
      </form>

      <form action='editemployee.php' method='post'>
        Change Schedule: <input type='text' name='schedule'> <input type='submit' value='Change Schedule' name='Change_Schedule'>
      </form>

      <form action='editemployee.php' method='post'>
        Change Password: <input type='text' name='pass'> <input type='submit' value='Change Password' name='Change_Password'>
      </form>";
}
else
{
 echo "<form action='editemployee.php' method='post'>
    Change Practice Interest: <input type='text' name='PracInt'> <input type='submit' value='Change Practice Interest' name='Change_PracInt'>
  </form>

  <form action='editemployee.php' method='post'>
    Change Status: <input type='text' name='status'> <input type='submit' value='Change Status' name='Change_Status'>
  </form>

  <form action='editemployee.php' method='post'>
    Change GP/Specialist Status: <input type='text' name='GPSpecial'> <input type='submit' value='Change GP/Specialist Status' name='Change_GPSpecial'>
  </form>

  <form action='editemployee.php' method='post'>
    Change Family/General Status: <input type='text' name='FamGen'> <input type='submit' value='Change Family/General Status' name='Change_FamGen'>
  </form>

  <form action='editemployee.php' method='post'>
    Change Specialty: <input type='text' name='specialty'> <input type='submit' value='Change Specialty' name='Change_Specialty'>
  </form>

  <form action='editemployee.php' method='post'>
    Change Password: <input type='text' name='pass'> <input type='submit' value='Change Password' name='Change_Password'>
  </form>


  ";
}

mysqli_close($con);

echo "
<br><a href=../home.php>Return to previous page</a>
</body>
</html>
";
