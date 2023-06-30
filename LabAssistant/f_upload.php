<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        // CONNECT DATABASE
        include('../connection.php');
        // USER ID
        $id=$_SESSION['id'];
    }
    //If a user is logged in and is not a lab-assistant
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='lab-assistant')
    {
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:../Admin/dash.php');    
		else if($role=='student')
			header('Location:../Student/dash.php');    
        else
            header('Location:../logout.php');
    }
    //If a user is not logged in
    else
    {
        header('Location:../logout.php');
    }
?>

<!DOCTYPE html>
<html>
<body>
    <!-- TEMPORARY DASHBOARD -->
    <div>
        <button onclick="window.location.href='dash.php'"> 
            Dashboard
        </button>
        <button onclick="window.location.href='view_equ.php'"> 
            View Equipment
        </button>
        <button onclick="window.location.href='lent_equ.php'"> 
            Lent Equipment
        </button>
        <button onclick="window.location.href='../logout.php'"> 
            Sign Out
        </button>        
    </div>
    <?php
    $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
    $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
    $labno=$row1['labno'];
    $dept=$row1['dept'];
    ?>

<form action="upload.php" method="post" enctype="multipart/form-data">
  Select excel file to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="text" name="labno" id="labno" value="<?php echo $labno; ?>" hidden>
  <input type="text" name="dept" id="dept" value="<?php echo $dept; ?>" hidden>
  <input type="submit" value="Upload Image" name="submit">
</form>

</body>
</html>