<?php
session_start();

if (isset($_POST['vis']))
{
  $_SESSION['vis'] = $_POST['vis'];
  $vis = $_SESSION['vis'];
}

else //for when returning to last page while in visitrequest.php
{ $vis = $_SESSION['vis'];}

$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_fetch_array(mysqli_query($con, "SELECT PatientPHN as 'PHN' FROM Visit WHERE Visit_ID=$vis"))['PHN'];
echo "<form action='../visits.php' method='post'>
<button type='submit' name='PHN' value='" . $result . "'>Click Here To View Patient Visit History</button></form>";

$archive = False; //True if the visit has ended and we are just showing a historical archive.
$result = mysqli_query($con, "SELECT End_Date FROM Visit WHERE Visit_ID=$vis AND End_Date IS NOT NULL");
if (0 != mysqli_num_rows($result))
{
  $archive = True;
  $result = mysqli_fetch_array($result)['End_Date'];
  echo "<strong>This page is an archive. This patient's visit has ended on $result.</strong><br>";
}

echo "<h2>Patient & Visit Profile</h2>";

$result = mysqli_query($con, "SELECT V.*, P.*, D.Name as 'DeptName'
FROM Patient AS P, Visit AS V, Department AS D
WHERE $vis=V.Visit_ID AND V.PatientPHN=P.PHN AND D.Department_ID IN (SELECT A.Dept_ID FROM Admitted AS A WHERE A.Visit_ID=$vis)");
$result = mysqli_fetch_array($result);

$phn = $result['PHN']; //Is used later...
$_SESSION['PHN'] = $phn;

foreach([["Patient Name", $result['name']],
["Visit ID", $result['Visit_ID']],
["Patient PHN", $result['PHN']],
["Admitting Department", $result['DeptName']],
["Patient Birthdate", $result['Birth']],
["Patient Address", $result['address']],
["Height", $result['height']],
["Weight", $result['weight']],
["Phone Number", $result['phone']],
["Diet", $result['Diet']],
["Visit Notes", $result['Notes']],
["Visit Start Date", $result['Start_Date']]] as $v)
{
  echo "<strong>" . $v[0] . "</strong>: " . $v[1] . "<br>";
}

$result = mysqli_query($con, "SELECT M.Type, M.ProcedureDateTime, M.Procedure_ID
FROM MedProcedure as M, Visit AS V
WHERE M.Visit_ID=V.Visit_ID AND V.Visit_ID=$vis AND V.End_Date IS NULL");
echo "<h2>List of Procedures</h2>";

if (0 < mysqli_num_rows($result))
{
  echo "<table border='1'>
  <tr> <th>Procedure Type</th> <th>Procedure ID</th> <th>Procedure Date Time</th> </tr>";

  while ($row = mysqli_fetch_array($result))
  {
    echo "<tr> <td> " . $row['Type'] . "</td> <td> ". $row['Procedure_ID'] . "</td> <td> " . $row['ProcedureDateTime'] . "</td>";
  }
  echo "</table> <br>";

  $procedures = mysqli_query($con, "SELECT Procedure_ID FROM MedProcedure WHERE Visit_ID=$vis");

  while ($medid = mysqli_fetch_array($procedures))
  {
    $medid = $medid['Procedure_ID'];

    $ast = mysqli_query($con, "SELECT E.Name, D.Name as 'Department' FROM Employee AS E, Department AS D
    WHERE D.Department_ID=E.Dept_ID AND E.Employee_ID IN
    (SELECT Employee_ID FROM Involved_IN WHERE Procedure_ID=$medid)");

    if (mysqli_num_rows($ast) > 0)
    {
      echo "Employees involved in procedure ID $medid: <ul>";
      while ($emp = mysqli_fetch_array($ast))
      {
        echo "<li>" . $emp['Name'] . " in " . $emp['Department'] . "</li>";
      }
      echo "</ul>";
    }
  }

  echo "To add employees to a procedure, place their IDs into this box (seperate employee IDs with spaces for the assistant boxes).
  <form action='visitrequest.php' method='post'>
  <input type='text' name='Proc_ID' placeholder='Enter Procedure ID here'>
  <input type='text' name='Assistants' placeholder='Enter employee IDs here'>
  <input type='submit' value='Request Procedure' name='Add_Ast'>
  </form>";

}
else
{
  echo "Patient has no scheduled procedures.<br>";
}

echo "<h2>List of Medicine</h2>";

$result = mysqli_query($con, "SELECT * FROM Medication WHERE Visit_ID=$vis");
if (0 < mysqli_num_rows($result))
{
  echo "<table border='1'>
  <tr> <th>Medication</th> <th>Dosage</th> <th>Frequency</th> <th>Preparation</th> </tr>";

  while ($row = mysqli_fetch_array($result))
  {
    echo "<tr> <td> " . $row['Medication'] . "</td> <td> " . $row['Dosage'] . "</td> <td> " . $row['Frequency'] . "</td> <td> " . $row['Preparation'];
  }
  echo "</table><br>";
}
else
{
  echo "No medication is registered under this visit.";
}

$result = mysqli_query($con, "SELECT * FROM Allergies WHERE PatientPHN=$phn");
if (0 < mysqli_num_rows($result))
{
  echo "<h2>List of Allergies</h2><ul>";
  while ($row = mysqli_fetch_array($result))
  {
    echo "<li>" . $row['Allergy'] . "</li>";
  }
  echo "</ul>";
}
else
{
  echo "Patient has no recorded allergies.<br>";
}

if ($archive)
{echo "<br><a href=../home.php>Return to previous page</a>";}
else
{
  echo "<h2>Bed Status</h2>";

  $result = mysqli_query($con, "SELECT O.*, D.Name, R.FeatureDesc, B.Needs_Cleaning
  FROM Department AS D, Occupied AS O, Room as R, Bed as B
  WHERE Visit_ID=$vis AND D.Department_ID=O.Dept_ID AND R.Room_Number=O.Room_Number AND O.Bed_Number=B.Bed_Number");

  if (0 < mysqli_num_rows($result))
  {
    $result = mysqli_fetch_array($result);
    echo "The patient is currently occupying bed number " . $result['Bed_Number'] . " in room " . $result['Room_Number'] . " in the " . $result['Name'] . " department.";

    if ($result['FeatureDesc'] == "NULL")
    { echo "<br>Description of room features:" . $result['FeatureDesc'] . "<br>"; }

    else
    { echo "<br>Room has no features."; }

    $form = "<form action='visitrequest.php' method='post'><input type='submit' value='Update Needs Cleaning Status' name='Cleaning'></form>";

    if ($result['Needs_Cleaning'] == "0")
    { echo "<br>This bed does not need cleaning. Click below to mark it as needing cleaning. $form";}

    else
    { echo "<br>This bed needs to be cleaned. Click below to mark it as cleaned. $form";}

    echo "<br>
    To reassign the patient to another bed, fill in this form:
      <form action='visitrequest.php' method='post'>
      Department ID: <input type='text' name='deptid'><br>
      Room Number: <input type='text' name='roomnum'><br>
      Bed Number: <input type='text' name='bednum'><br>
      <input type='submit' value='Reassign Bed' name='Reassign_Bed'></form>
      <br>

      To discharge the patient from the bed, click this button.
      <form action='visitrequest.php' method='post'> <input type='submit' value='Discharge' name='Discharge'></form>

    ";
  }
  else
  {
    echo "The patient is currently not assigned to a bed.<br>
    Fill in the following from to assign them to one: <form action='visitrequest.php' method='post'>
    Department ID: <input type='text' name='deptid'><br>
    Room Number: <input type='text' name='roomnum'><br>
    Bed Number: <input type='text' name='bednum'><br>
    <input type='submit' value='Assign Bed' name='assignbed'></form>

    For a list of department IDs, consult the Departments directory.
    ";
  }

  echo "<h2>Manage Visit</h2>

  <form action='visitrequest.php' method='post'>
     Change Diet: <input type='text' name='Diet'> <input type='submit' value='Change Diet' name='Change_Diet'>
   </form>

   <form action='visitrequest.php' method='post'>
     Add Patient Allergy: <input type='text' name='Allergy'> <input type='submit' value='Add Allergy' name='Add_Allergy'>
   </form>

   <form action='visitrequest.php' method='post'>
     Change Visit Notes: <input type='text' name='Notes'> <input type='submit' value='Change Notes' name='Change_Notes'>
   </form>

   <form action='visitrequest.php' method='post'>
    New Medication:
      <input type='text' name='Medication' placeholder='Enter Medication Name...'>
      <input type='text' name='Dosage' placeholder='Enter Dosage...'>
      <input type='text' name='Frequency' placeholder='Enter Frequency...'>
      <input type='text' name='Preparation' placeholder='Enter Preparation...'>
      <input type='submit' value='Add Medication' name='Add_Medication'>
    </form>

  <h2>Manage Patient Profile</h2>

  <form action='visitrequest.php' method='post'>
     Change Address: <input type='text' name='Address'> <input type='submit' value='Change Address' name='Change_Address'>
   </form>

   <form action='visitrequest.php' method='post'>
      Change Height: <input type='text' name='Height'> <input type='submit' value='Change Height' name='Change_Height'>
    </form>

    <form action='visitrequest.php' method='post'>
       Change Weight: <input type='text' name='Weight'> <input type='submit' value='Change Weight' name='Change_Weight'>
     </form>

     <form action='visitrequest.php' method='post'>
        Change Phone Number: <input type='text' name='Phone'> <input type='submit' value='Change Phone Number' name='Change_Phone'>
      </form>

  <h2>Request New Procedure</h2>
   <form action='visitrequest.php' method='post'>
    Request New Procedure. For the 'Assistants' box, enter a space separated list of employee and/or physician IDs; they will be listed as involved in this procedure.
      <input type='text' name='Proc_ID' placeholder='Enter procedure ID'>
      <input type='text' name='Type' placeholder='Enter description'><br>
      <input type='datetime-local' name='datetime'>
      <input type='text' name='Assistants' placeholder='Assistants'>
      <input type='submit' value='Request Procedure' name='Req_Proc'>
    </form>

    <h2>End Visit</h2>
    <form action='visitrequest.php' method='post'>
      End visit. Click here to end the visit and discharge the patient from their bed (if they are occupying one).
      <input type='submit' value='End Visit' name='End_Visit'>
    </form>

  <br><a href=../home.php>Return to previous page</a>";
}
?>
