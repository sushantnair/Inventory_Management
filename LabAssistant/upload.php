<?php 
    session_start();
    include('../Components/SimpleXLS.php');
    include('../Components/SimpleXLSX.php');
    use Shuchkin\SimpleXLS;
    use Shuchkin\SimpleXLSX;
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
			header('Location:../Admin/index.php');    
		else if($role=='student')
			header('Location:../Student/index.php');    
        else
            header('Location:../logout.php');
    }
    //If a user is not logged in
    else
    {
        header('Location:../logout.php');
    }

    if(isset($_FILES['fileToUpload'])){
        // include('../connection.php');
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


        // Check if file already exists
        if (file_exists($target_file)) {
            echo '<div class="alert alert-danger">Sorry, file already exists.</div>';
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo '<div class="alert alert-danger">Sorry, your file is too large.</div>';
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "csv" && $imageFileType != "xlsx" && $imageFileType != "xls") {
            echo '<div class="alert alert-danger">Sorry, only CSV, XLS, and XLSX files are allowed.</div>';
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo '<div class="alert alert-danger">Sorry, your file was not uploaded.</div>';
        } 
        else 
        {    
            if (copy($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
            {
                echo '<div class="alert alert-success">The file '. htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . ' has been uploaded.</div>';
            } else 
            {
                echo '<div class="alert alert-danger">Sorryy, there was an error uploading your file.</div>';
            }
        }

        if ($uploadOk != 0) {
            if($imageFileType == "csv"){
            if (($open = fopen($target_file, "r")) !== false) {
                while (($data = fgetcsv($open, 1000, ",")) !== false) {
                    $array[] = $data;
                }
                fclose($open);
            }
            } else if($imageFileType == "xlsx"){

            if ( $xlsx = SimpleXLSX::parse($target_file) ) {
                $array = $xlsx->rows();
            } else {
                echo SimpleXLSX::parseError();
            }
            } else if($imageFileType == "xls"){

            if ( $xls = SimpleXLS::parse($target_file) ) {
                $array = $xls->rows();
            } else {
                echo SimpleXLS::parseError();
            }
            }

            for ($row = 1; $row < count($array); $row++) {
                $dept = $_POST['dept'];
                $labno = $_POST['labno'];
                $eqname = $array[$row][0];
                $dsrno = $array[$row][1];
                
                $fetch_short_dept=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM departments WHERE dept='$dept'"),MYSQLI_ASSOC);
                $short=$fetch_short_dept['short'];
                $dsr="KJSCE/".$short."/".$labno."/".$dsrno;
                
                $eqtype = $array[$row][2];
                $quantity = $array[$row][3];
                $desc1 = $array[$row][4];
                $desc2 = $array[$row][5];
                $cost = $array[$row][6]; 
                $sql2=mysqli_query($conn,"SELECT * FROM $labno WHERE eqname='$eqname' AND dsrno='$dsr'");
                if(mysqli_num_rows($sql2)==0)
                {
                    // IF NO SAME EQUIPMENT WITH SAME NAME AND SAME DSR-NUMBER STORED EARLIER
                    if(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsr'"))==0)
                    {
                        mysqli_query($conn,"INSERT INTO $labno(eqname,eqtype,dsrno,quantity,desc1,desc2,cost) values('$eqname','$eqtype','$dsr',$quantity,'$desc1','$desc2',$cost)");
                    }
                    else 
                    {
                        // INVALID INPUT
                        // SAME DSR NUMBER DIFFERENT EQUIPMENT NAME
                    }
                }
                else 
                {
                    // SAME EQUIPMENT PRESENT, UPDATE QUANTITY 
                    $row2=mysqli_fetch_array($sql2,MYSQLI_ASSOC);
                    // $qu=$row2['quantity'];  // OLD QUANTITY
                    mysqli_query($conn, "DELETE from $labno where eqname='$eqname' AND dsrno='$dsr'");
                    mysqli_query($conn,"INSERT INTO $labno(eqname,eqtype,dsrno,quantity,desc1,desc2,cost) values('$eqname','$eqtype','$dsr',$quantity,'$desc1','$desc2',$cost)");
                }
            }

            echo '<div class="alert alert-success">Added to database!</div>';
            unlink($target_file);
            header('Location: view.php');
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IM-KJSCE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="CSS/styles.css">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->

</head>
    </head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute container row w-100 top-0 ms-4" style="left: 100px; z-index:100;">
    <?php
    $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
    $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
    $labno=$row1['labno'];
    $dept=$row1['dept'];
    ?>
<br>
<br>
<br>
<hr>
<hr>
<br>
<form action="upload.php" method="post" enctype="multipart/form-data">
  Select excel file to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="text" name="labno" id="labno" value="<?php echo $labno; ?>" hidden>
  <input type="text" name="dept" id="dept" value="<?php echo $dept; ?>" hidden>
  <input type="submit" value="Upload Excel file" name="submit">
</form>
<br>
<br>
<hr>
    </div>
</body>
</html>