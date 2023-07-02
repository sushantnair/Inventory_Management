<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        // CONNECT DATABASE
        include('../connection.php');
        // USER ID
        $id=$_SESSION['id'];
        // IF ADDING EQUIPMENT
        if(isset($_POST['addeq']))
        {
            
            // GET DATA FROM FORM
            $eqname=$_POST['eqname'];
            $eqtype=$_POST['eqtype'];
            $dsrno=$_POST['dsrno'];
            $quantity=$_POST['quantity'];   
            $desc1=$_POST['desc1'];
            $desc2=$_POST['desc2'];
            $cost=$_POST['cost'];
            if($eqtype!=0)
            {
                //GET LAB-NUMBER FROM LAB TABLE USING SESSION ID (ASSISTANT ID)
                $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
                
                $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
                if(!$row1)
                {
                    echo mysqli_error($conn);
                    die();
                }
                
                $labno=$row1['labno'];   //LAB-NUMBER
                $dept=$row1['dept'];
                $dsr="KJSCE/".$dept."/".$labno."/".$dsrno;


                // echo $labno;
                // SELECT EQUIPMENT WITH SAME NAME AND SAME DSR-NUMBER
                $sql2=mysqli_query($conn,"SELECT * FROM $labno WHERE eqname='$eqname' AND dsrno='$dsr'");
                if(mysqli_num_rows($sql2)==0)
                {
                    // IF NO SAME EQUIPMENT WITH SAME NAME AND SAME DSR-NUMBER STORED EARLIER
                    if(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsr'"))==0)
                    {
                        mysqli_query($conn,"INSERT INTO $labno(eqname,eqtype,dsrno,quantity,desc1,desc2,cost) values('$eqname','$eqtype','$dsr',$quantity,'$desc1','$desc2','$cost')");
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
                    $qu=$row2['quantity'];  // OLD QUANTITY
                    mysqli_query($conn,"UPDATE $labno set quantity=($quantity+$qu) WHERE dsrno='$dsrno'");
                }
            }
            
        }
        if(isset($_POST['delete'])) //IF DELETING EQUIPMENT
        {
            // $eqname=$_POST['eqname'];
            $dsrno=$_POST['dsrno'];
            $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
            $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
            $labno=$row1['labno'];
            $sql1=mysqli_query($conn,"DELETE FROM $labno WHERE dsrno='$dsrno'");
        }
        if(isset($_POST['return']))
        {
            $lendto=$_POST['labno'];
            $dsrno=$_POST['dsrno'];

            //FIND LENDING LAB DETAILS
            $query=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno'");
            $row=mysqli_fetch_array($query,MYSQLI_ASSOC);
            $lendfrom=$row['lendfrom'];
            $lendquan=$row['lendquan'];
            $remove_lend=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom'");
            if(!$remove_lend)
            {
                echo "ERR1";
                echo mysqli_error($conn);
                die();
            }
            else
            {
                $remove_lendfrom=mysqli_query($conn,"DELETE FROM $lendto WHERE dsrno='$dsrno'");
                $remove_lendto1=mysqli_query($conn,"UPDATE $lendfrom SET toquan=0 WHERE dsrno='$dsrno'");
                $remove_lendto2=mysqli_query($conn,"UPDATE $lendfrom SET quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");
                if(!$remove_lendfrom)
                {
                    echo "ERR2";
                    echo mysqli_error($conn);
                    die();
                }
                if(!$remove_lendto1)
                {
                    echo "ERR3";
                    echo mysqli_error($conn);
                    die();
                }
                if(!$remove_lendto2)
                {
                    echo "ERR4";
                    echo mysqli_error($conn);
                    die();
                }
            }
        }
        
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

    if (isset($_POST['update'])) {
      $eqname = $_POST['name'];
      $eqtype = $_POST['type'];
      $dsrno = $_POST['dsr'];
      $quantity = $_POST['quant'];
      $desc1 = $_POST['description1'];
      $desc2 = $_POST['description2'];
      $cost = $_POST['cost'];

      $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
              
      $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
      if(!$row1)
      {
          echo mysqli_error($conn);
          die();
      }
      
      $labno=$row1['labno'];   //LAB-NUMBER

      $updateQuery = "UPDATE $labno SET eqname='$eqname', eqtype='$eqtype', dsrno='$dsrno', quantity='$quantity', desc1='$desc1', desc2='$desc2', cost='$cost' WHERE dsrno='$dsrno'";
      $result = mysqli_query($conn, $updateQuery);

      if ($result) {
          // Update successful
          echo "Update successful!";
      } else {
          // Update failed
          echo "Update failed: " . mysqli_error($conn);
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IM-KJSCE</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <style>
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 400px;
      background-color: #f1f1f1;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }

    .form-group textarea {
      height: 80px;
    }

    .form-group input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      cursor: pointer;
    }

    .form-group input[type="submit"]:hover {
      background-color: #45a049;
    }

    .form-group button {
      background-color: #ccc;
      color: black;
      cursor: pointer;
    }

    .form-group button:hover {
      background-color: #999;
    }
  </style>
</head>
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
    <!-- Search bar -->
    
    <form action="" method="post" style="text-align:center;">
        <input type="text" name="search" id="search" style="text-align:center;" placeholder="Enter equipment which you want to search for">
        <br>
        <input type="submit" value="Search">
    </form>
    <!-- MAIN TABLE  -->
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Name<br></th>
                    <th scope="col">Type<br></th>
                    <th scope="col">DSR</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Lent Quantity</th>
                    <th scope="col">Description 1</th>
                    <th scope="col">Description 2</th>
                    <th scope="col">Cost</th>
                    <th scope="col">Update<br></th>
                </tr>
            </thead>
            
            <tbody>
                <tr>
                    <!-- FORM FOR INPUTTING EQUIPMENT  -->
                    <form action="view_equ.php" method="post">
                        <!-- placeholder helps when the table headers are not visible without scrolling to the top -->
                    <td><input type="text" name='eqname' id='eqname' placeholder="Enter Equipment Name" required></td>
                    <td>
                        <select id="eqtype" name="eqtype" placeholder="Equipment Type" required>
                            <option value="0" selected>None</option>
                            <option value="Software">Software</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Furniture">Furniture</option>
                        </select>
                    </td>
                    <td><input type="text" name='dsrno' id='dsrno' placeholder="DSR No." required></td>
                    <td><input type="number" name='quantity' id='quantity' placeholder="Quantity" required></td>
                    <td></td>
                    <td><input type="text" name='desc1' placeholder="Description 1" id='desc1'></td>
                    <td><input type="text" name='desc2' placeholder="Description 2" id='desc2'></td>
                    <td><input type="number" step="0.01" name='cost' placeholder="Cost" id='cost'></td>

                    <td>
                        <button class="button1" type="submit" name="addeq"> 
                            Add
                        </button>
                    </td>
                    </form>
                </tr>
                <?php
                    //FETCH LAB-NUMBER USING SESSION ID
                    $sql1=mysqli_query($conn,"SELECT * FROM labs WHERE assistid=$id");
                    $row1 = mysqli_fetch_array($sql1,MYSQLI_ASSOC);
                    $labno=$row1['labno'];

                    //FETCH LAB TABLE USING LAB-NUMBER
                    $table=mysqli_query($conn,"SELECT * FROM $labno");
                    $v=1;
                    while($row = mysqli_fetch_array($table,MYSQLI_ASSOC))
                    {
                        ?>
                        <tr>
                            <td><?php echo $row['eqname'];?></td>
                            <td><?php echo $row['eqtype'];?></td>
                            <td><?php echo $row['dsrno'];?></td>
                            <td><?php echo $row['quantity'];?></td>
                            <td><?php echo $row['toquan']?></td>
                            <td><?php echo $row['desc1'];?></td>
                            <td><?php echo $row['desc2'];?></td>
                            <td><?php echo $row['cost'];?></td>
                            <td>
                            <?php 
                            if($row['byquan']==0)
                            {
                                ?>
                                <button class="button1" onclick="openPopup(<?php echo$v;?>)"> 
                                        Update
                                    </button>
                                <form action="view_equ.php" method="post">
                                <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <button class="button1" type="submit" name="delete"> 
                                        Delete
                                    </button>
                                </form>
                                
                                <?php 
                            }
                            else 
                            {
                                ?>
                                <form action="view_equ.php" method="post">
                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <button class="button1" type="submit" name="return"> 
                                        Return
                                    </button>
                                </form>
                                <?php
                            }
                            ?>
                            <form action="lend.php" method="post">
                                    <input type="text" name="dsrno" value="<?php echo $row['dsrno']; ?>" style="display:none;">
                                    <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
                                    <button class="button1" type="submit" name="lend"> 
                                        Lend
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php
                        $v=$v+1;
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div id="popup" class="popup">
    <h2>Update Form</h2>
    <form action="" method="post" id="updateForm">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name">
      </div>
      <div class="form-group">
        <label for="type">Type:</label>
        <input type="text" id="type" name="type">
      </div>
      <div class="form-group">
        <label for="dsr">DSR No.:</label>
        <input type="text" id="dsr" name="dsr">
      </div>
      <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quant" name="quant">
      </div>
      <div class="form-group">
        <label for="description1">Description 1:</label>
        <textarea id="description1" name="description1"></textarea>
      </div>
      <div class="form-group">
        <label for="description2">Description 2:</label>
        <textarea id="description2" name="description2"></textarea>
      </div>
      <div class="form-group">
        <label for="cost">Cost:</label>
        <input type="number" id="costin" name="cost">
      </div>
      <div class="form-group">
        <input type="submit" name="update" value="update">
      </div>
    </form>
    <button onclick="closePopup()">Close</button>
  </div>

  <script>
function openPopup(rowId) {
  var popup = document.getElementById("popup");
  popup.style.display = "block";

  // Get the data from the table row
  var tableRows = document.getElementsByTagName("tr");
  var targetRow = tableRows[rowId + 1]; // Add 1 to skip the form row
  var cells = targetRow.getElementsByTagName("td");

  // Populate the form fields with the data
  document.getElementById("name").value = cells[0].innerHTML;
  document.getElementById("type").value = cells[1].innerHTML;
  document.getElementById("dsr").value = cells[2].innerHTML;
  document.getElementById("quant").value = cells[3].innerText;
  document.getElementById("description1").value = cells[5].innerHTML;
  document.getElementById("description2").value = cells[6].innerHTML;
  document.getElementById("costin").value = cells[7].innerHTML;
}

function closePopup() {
      var popup = document.getElementById("popup");
      popup.style.display = "none";
    }
  </script>
</body>
</html>