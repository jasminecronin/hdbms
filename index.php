<?php session_start();
if (isset($_POST['Logout'])) //True if user clicked the logout button
{
  session_unset();
  session_destroy();
  header("location:index.php");
  exit();
}?>

<html>
<body>

<form action="index.php" method="post">
   User ID: <input type="text" placeholder="Enter user ID" name="ID"><br>
   Password: <input type="password" placeholder="Enter password" name="pass"><br>
   <input type="submit" value="Log In">
</form>

</body>
</html>

<?php
$id = "";
$pass = "";
if (!empty($_POST))
{
  $id = $_POST["ID"];
  $pass = $_POST["pass"];

  if(!is_numeric($id)) //prevent the error message that would show up in the sql query if user enters a non-integer
  { $id = -1; }

  // Create connection
  $con=mysqli_connect("localhost","root","","471");

  // Check connection
  if (mysqli_connect_errno($con))
  { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }

  $sql = mysqli_query($con, "SELECT Admin, Employee_ID FROM Employee WHERE Employee_ID=$id AND Password='$pass'");
  if (0 == mysqli_num_rows($sql))
  {
    $sql = mysqli_query($con, "SELECT Prac_ID FROM Physician WHERE Prac_ID=$id AND Password='$pass'");
    if (0 == mysqli_num_rows($sql))
    {
      echo "Login failed. Please try again. <br>";
    }
    else
    {
      $temp = mysqli_fetch_array($sql);
      $_SESSION['Admin'] = "0"; //All physicians are not admins
      $_SESSION['ID'] = $temp["Prac_ID"];
      $_SESSION['EmpPhys'] = "P";
      header("Location:home.php");
      exit();

    }
  }
  else
  {
    $temp = mysqli_fetch_array($sql);
    $_SESSION['Admin'] = $temp["Admin"];
    $_SESSION['ID'] = $temp["Employee_ID"];
    $_SESSION['EmpPhys'] = "E";
    header("Location:home.php");
    exit();
  };
  mysqli_close($con);
}


?>
