<?php
include('../connection.php');
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
//   $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
//   if($check !== false) {
//     echo "File is a spreadsheet - " . $check["mime"] . ".";
//     $uploadOk = 1;
//   } else {
//     echo "File is not a spreadsheet.";
//     $uploadOk = 0;
//   }
// }

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "csv") {
  echo "Sorry, only CSV files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}

if ($uploadOk != 0) {
    if (($open = fopen($target_file, "r")) !== false) {
        while (($data = fgetcsv($open, 1000, ",")) !== false) {
            $array[] = $data;
        }
        fclose($open);
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

    echo 'Added to database!';
    unlink($target_file);
    header('Location: view_equ.php');
}

?>