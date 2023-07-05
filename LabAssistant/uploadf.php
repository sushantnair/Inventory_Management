<?php
include('../connection.php');
include('../Components/SimpleXLS.php');
include('../Components/SimpleXLSX.php');
use Shuchkin\SimpleXLS;
use Shuchkin\SimpleXLSX;
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <div class="container">
        <h2>Upload File</h2>

        <?php
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
        } else {
          if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo '<div class="alert alert-success">The file '. htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . ' has been uploaded.</div>';
          } else {
            echo '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
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
                $dsr = "KJSCE/".$dept."/".$labno."/".$dsrno;
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
        ?>
    </div>

    <!-- Add Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>