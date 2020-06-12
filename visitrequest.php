<?php
session_start();
$id = $_SESSION['ID'];
$vis = $_SESSION['vis'];
$phn = $_SESSION['PHN'];

$con = mysqli_connect("localhost", "root", "", "471");
if (mysqli_connect_errno($con))
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

function quotes($in) { return "\"" . $in . "\""; }

function output($conn, $rresult)
{
  if (1 == mysqli_affected_rows($conn))
  { echo "<strong> Update successful; 1 row altered.</strong><br>"; }
  else
  {
    echo "<strong> Update unsuccessful. Number of records updated: " . mysqli_affected_rows($conn) . "</strong>";
  }
}

function addAssistants($ProcID, $assistants)
{
  $con = mysqli_connect("localhost", "root", "", "471");
  $result = mysqli_query($con, "SELECT * FROM MedProcedure WHERE Procedure_ID=$ProcID");
  if (1 != mysqli_num_rows($result))
  {
    echo "Error: Procedure ID $ProcID not found.";
  }
  else
  {
    foreach ($assistants as $ast)
    {
      $result = mysqli_query($con, "SELECT * FROM EMPLOYEE WHERE Employee_ID=$ast");

      if (0 == mysqli_num_rows($result))
      {
        echo "Error: No employee with ID $ast was found. Not added.<br>";
      }
      else
      {
        if (1 == mysqli_num_rows(mysqli_query($con, "SELECT * FROM INVOLVED_IN WHERE Procedure_ID=$ProcID AND Employee_ID=$ast")))
        {
          echo "Assistant with employee ID $ast is already registered for this procedure.<br>";
        }
        else
        {
          $result = mysqli_query($con, "INSERT INTO INVOLVED_IN (Employee_ID, Procedure_ID) VALUES ($ast, $ProcID)");
          if (1 == mysqli_affected_rows($con))
          {
            echo "Assistant with employee ID $ast added.<br>";
          }
          else
          {
            echo "Error: Assistant with employee ID $ast was found but could not be added.<br>";
          }
        }
      }
    }
  }
}

function assignbed() //Two of the form actions call this.
{
  global $vis;
  global $con;

  $roomnum = $_POST['roomnum'];
  $bednum = $_POST['bednum'];
  $deptid = $_POST['deptid'];
  if (!(is_numeric($roomnum) AND is_numeric($bednum) AND is_numeric($deptid)))
  {
    echo "Error: One of the entered values was not an integer.";
  }
  else
  {
    $result = mysqli_query($con, "SELECT * FROM BED WHERE Dept_ID=$deptid
    AND Room_Number=$roomnum AND Bed_Number=$bednum");
    if (0 == mysqli_num_rows($result)) //ensure bed exists
    {
      echo "Error: Bed not found. Ensure correct department ID, room number and bed entered.<br>Consult the bed directory to find a list of available beds.<br>";
    }
    else if (1 == mysqli_num_rows(mysqli_query($con, "SELECT * FROM OCCUPIED WHERE Dept_ID=$deptid
    AND Room_Number=$roomnum AND Bed_Number=$bednum"))) //ensure bed is unoccupied
    {
      echo "Error: Bed is already occupied. Consult the bed directory to find a list of available beds.";
    }
    else //insert record
    {
      $result = mysqli_query($con, "INSERT INTO OCCUPIED (Visit_ID, Dept_ID, Room_Number, Bed_Number)
      VALUES ($vis, $deptid, $roomnum, $bednum)");
      echo "Record successfully added. Patient is now occupying bed $bednum in room $roomnum.<br>";
    }
  }
}

if (isset($_POST['Change_Address']))
{
  $add = quotes($_POST['Address']);
  $result = mysqli_query($con, "UPDATE Patient SET address=$add WHERE PHN=$phn");
  output($con, $result);
}

else if (isset($_POST['Change_Height']))
{
  $height = $_POST['Height'];
  if (!is_numeric($height))
  {
    echo "Error: The entered value was not an integer.";
  }
  else
  {
    $result = mysqli_query($con, "UPDATE Patient SET height=$height WHERE PHN=$phn");
    output($con, $result);
  }
}

else if (isset($_POST['Change_Weight']))
{
  $weight = $_POST['Weight'];
  if (!is_numeric($weight))
  {
    echo "Error: The entered value was not an integer.";
  }
  else
  {
    $result = mysqli_query($con, "UPDATE Patient SET weight=$weight WHERE PHN=$phn");
    output($con, $result);
  }
}

else if (isset($_POST['Change_Phone']))
{
  $phone = $_POST['Phone'];
  if (!is_numeric($phone))
  {
    echo "Error: The entered value was not an integer.";
  }
  else
  {
    $result = mysqli_query($con, "UPDATE Patient SET phone=$phone WHERE PHN=$phn");
    output($con, $result);
  }
}

else if (isset($_POST['assignbed']))
{
  assignbed();
}

else if (isset($_POST['Reassign_Bed']))
{
  $result = mysqli_query($con, "DELETE FROM Occupied WHERE Visit_ID=$vis");
  if (0 == mysqli_affected_rows($con))
  {
    echo "Unsuccessful discharge from previous bed. 0 rows affected...";
  }
  else
  {
    echo "Successful discharge from previous bed. <br>Result for assigning to new bed: <br>";
    assignbed();
  }
}

else if (isset($_POST['Change_Diet']))
{
  $diet = quotes($_POST['Diet']);
  $result = mysqli_query($con, "UPDATE Visit SET Diet=$diet WHERE Visit_ID=$vis");
  output($con, $result);
}

else if (isset($_POST['Add_Allergy']))
{
  $all = quotes($_POST['Allergy']);
  $result = mysqli_query($con, "INSERT INTO Allergies (Allergy, PatientPHN) VALUES ($all, $phn)");
  output($con, $result);
}

else if (isset($_POST['Change_Notes']))
{
  $note = quotes($_POST['Notes']);
  $result = mysqli_query($con, "UPDATE Visit SET Notes=$note WHERE Visit_ID=$vis");
  output($con, $result);
}

else if (isset($_POST['Discharge']))
{
  $result = mysqli_query($con, "DELETE FROM Occupied WHERE Visit_ID=$vis");
  output($con, $result);
}

else if (isset($_POST['Cleaning']))
{
  $result = mysqli_query($con, "UPDATE Bed as B SET Needs_Cleaning=NOT Needs_Cleaning WHERE EXISTS
    (SELECT * FROM Occupied as O WHERE O.Visit_ID=$vis AND B.Bed_Number=O.Bed_Number AND B.Room_Number=O.Room_Number AND B.Dept_ID=O.Dept_ID)
");
  output($con, $result);
}

else if (isset($_POST['Add_Medication']))
{
  $med = quotes($_POST['Medication']);
  $dos = quotes($_POST['Dosage']);
  $freq = quotes($_POST['Frequency']);
  $prep = quotes($_POST['Preparation']);
  $result = mysqli_query($con, "INSERT INTO MEDICATION (Visit_ID, Medication, Dosage, Frequency, Preparation) VALUES
  ($vis, $med, $dos, $freq, $prep)");
  output($con, $result);
}

/*
echo "To add employees to a procedure, place their IDs into this box (seperate employee IDs with spaces for the assistant boxes).
<form action='visitrequest.php' method='post'>
<input type='text' name='Proc_ID' placeholder='Enter Procedure ID here'>
<input type='text' name='Assistants' placeholder='Enter employee IDs here'>
<input type='submit' value='Request Procedure' name='Add_Ast'>
</form>"
*/
else if (isset($_POST['Add_Ast']))
{
  addAssistants($_POST['Proc_ID'], array_unique(explode(" ", $_POST['Assistants'])));
}

else if (isset($_POST['Req_Proc']))
{
  $Proc_ID = $_POST['Proc_ID'];
  $error = False;

  if (!is_numeric($Proc_ID))
  {
    echo "Error: One of the entered values for physician/assistant ID was not an integer. Please ensure correct values entered.";
  }

  else
  {
    $type = quotes($_POST['Type']);
    $dtime = explode("T", $_POST['datetime']);
    $dtime = quotes($dtime[0] . " " . $dtime[1] . ":00");

    $result = mysqli_query($con, "INSERT INTO MedProcedure (Procedure_ID, Type, ProcedureDateTime, Visit_ID, RequestingPhysician_ID) VALUES
    ($Proc_ID, $type, $dtime, $vis, $id)");
    if (1 == mysqli_affected_rows($con))
    {
      echo "Successful procedure creation. Results for employee addition: <br>";
      addAssistants($_POST['Proc_ID'], array_unique(explode(" ", $_POST['Assistants'])));
    }
  }
}

else if(isset($_POST['Confirm']))
{
  $result = mysqli_query($con, "DELETE FROM Occupied WHERE Visit_ID=$vis");
  $result = mysqli_query($con, "UPDATE Visit SET End_Date=current_date() WHERE Visit_ID=$vis");
  if (1 == mysqli_affected_rows($con))
  {
    echo "Visit ending successful. <a href=home.php>Click here to return to home page.</a>";
  }
}

else if(isset($_POST['End_Visit']))
{
  echo "Are you sure you'd like to end this vist? Click here to end visit $vis for patient with PHN $phn and end the visit.";

  echo "<form method='post' action='visitrequest.php'><input type='submit' name='Confirm' value='Confirm'></form><br>";
}


 if (!isset($_POST['Confirm']))
 {echo "<br><a href=visitinfo.php>Return to previous page</a>";}
?>
