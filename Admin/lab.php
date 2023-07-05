<?php 
    session_start();
    $dept1=$_SESSION['dept'];
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        include '../connection.php';
        if(isset($_POST['assist']))
        //UPDATE ASSISTANTS
        {  
            $assistid=$_POST['assistant'];
            $labno=$_POST['labno'];  
            if($assistid!=0) 
            {
                $query1=mysqli_query($conn,"SELECT * from user where id=$assistid");
                $row = mysqli_fetch_array($query1,MYSQLI_ASSOC);
                $name=$row['name'];
                mysqli_query($conn,"UPDATE labs SET assistid=$assistid, assistname='$name' WHERE labno='$labno'");
            }
            else 
            {
                mysqli_query($conn,"UPDATE labs SET assistid=0, assistname='' WHERE labno='$labno'");
            }
            header('Location:lab.php');

            
        }  
        if(isset($_POST['lab']))
        {    
            // DELETE LAB
            $labno=$_POST['labno']; // LAB-NUMBER
            mysqli_query($conn,"DELETE FROM labs WHERE labno='$labno'"); // DELETE LAB FROM LABS TABLE
            mysqli_query($conn,"DROP TABLE $labno");    //DROP LAB TABLE 
            header('Location:lab.php');

        }
        if(isset($_POST['addlab']) && $_POST['dept']!='None')   // CREATE LAB
        {
            //FORM DATA
            $labno=$_POST['labno']; 
            $labname=$_POST['labname']; 
            $dept=$_POST['dept']; 
            $active=$_POST['active']; 
            //INSERT LAB DETAILS IN LABS TABLE
            $insert_lab=mysqli_query($conn,"INSERT INTO labs (labname,dept,labno,active,assistname,assistid) values('$labname','$dept','$labno','$active','',0)");
            
            //CREATE NEW TABLE FOR LAB USING LAB-NUMBER
            $create_lab=mysqli_query($conn,"CREATE TABLE $labno (eqname VARCHAR(250), dsrno VARCHAR(250), eqtype VARCHAR(250), quantity INT(4), desc1 VARCHAR(250), desc2 VARCHAR(250), cost FLOAT(10),toquan INT DEFAULT 0,byquan INT DEFAULT 0,PRIMARY KEY (dsrno))");
            header('Location:lab.php');

        }    
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/index.php');    
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="./CSS/styles.css">
</head>
<body style="overflow-x: hidden;">
     <?php include('../Components/sidebar.php') ?>
    <!-- TEMPORARY DASHBOARD -->
    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-light" >
  <a class="navbar-brand" href="index.php"><button onclick="window.location.href='index.php'"> 
            Dashboard
        </button></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      
      <li class="nav-item">
        <a class="nav-link" href="assist.php"><button onclick="window.location.href='assist.php'"> 
            Manage Lab Assistants
        </button> </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="lab.php"><button onclick="window.location.href='lab.php'">
            Manage Labs
        </button></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../logout.php"><button onclick="window.location.href='../logout.php'">
            Signout
        </button></a>
      </li>
      
    </ul>
    
  </div>
</nav> -->
    
<div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; z-index:100; width: calc(100% - 100px);">
    <form action="" method="post" style="text-align:center;">
        <input type="text" name="search" id="search" style="text-align:center;" placeholder="Search">
        <br><br>
        <label for="assigned">Lab Assistant Assigned?</label>
        <select id="assigned" name="assigned">
            <option value="">Any</option>
            <option value="and assistname!=''">Yes</option>
            <option value="and assistname=''">No</option>
        </select>
        <br><br>
        <label for="sta">Active</label>
        <select id="sta" name="sta">
            <option value="">Any</option>
            <option value="and active='yes'">Yes</option>
            <option value="and active='no'">No</option>
        </select>
        <br><br>
        <input class="btn btn-outline-danger alert-danger" type="submit" value="Search"><br><br>
    </form>
    <!-- TABLE DISPLAY  -->
    <div class="row col-lg-12 card card-body">
        <table class="mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Lab Num<br></th>
                    <th scope="col">Lab Name</th>
                    <th scope="col">Department</th>
                    <th scope="col">Active</th>
                    <th scope="col">Lab Assistant</th>
                    <th scope="col">Update<br></th>
                </tr>
            </thead>
            
            <tbody>
            <tr>
                    <form action="" method="post">
                        <td><input type="text" name='labno' id='labno' required></td>
                        <td><input type="text" name='labname' id='labname' required></td>
                        <td>
                            <select id="dept" name="dept" required>
                                <option value="" disabled selected>None</option>
                                <?php 
                                if($dept1!=NULL){
                                    ?>
                                    <option value=<?php echo $dept1; ?>><?php echo $dept1; ?></option>
                                    <?php
                                }
                                else{
                                $fetch_departments=mysqli_query($conn,"SELECT * FROM departments");
                                while($dept_row=mysqli_fetch_array($fetch_departments,MYSQLI_ASSOC))
                                {
                                    ?>
                                    <option value=<?php echo $dept_row['dept']; ?>><?php echo $dept_row['dept']; ?></option>
                                    <?php
                                }
                            }
                                ?>
                            </select>
                        </td>
                        <td> 
                            <select id="active" name="active" required>
                                <option selected value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </td>
                        <td>
                            <i>(Assign assistant after creating lab)</i>
                        </td>
                        <td>
                            <button class="btn btn-outline-dark" type="submit" name="addlab" style="font-weight: bold; border-width: 2px;"> 
                                Create Lab
                            </button>
                        </td>
                    </form>
                </tr>
                <!-- EACH ROW IN WHILE LOOP DISPLAYING ALL LABS -->
                <?php
                    if (isset($_POST['search'])) 
                    {
                        $search=$_POST['search'];
                        $assign=$_POST['assigned'];
                        $active=$_POST['sta'];
                        $sql_table_display = "SELECT * 
                                            FROM labs 
                                            where dept='$dept1' and (labno like '%$search%' OR 
                                                    labname like '%$search%' OR 
                                                    assistname like '%$search%' ) 
                                            $assign $active";
                        $result_table_display = mysqli_query($conn, $sql_table_display);
                        if(!$result_table_display){
                            echo "There is some problem in the connection or search error";
                            return;
                        }
                    }
                    else 
                    {
                        if($dept1!=NULL){
                        $sql_table_display = "SELECT * 
                                            FROM labs WHERE dept='$dept1'";
                        }
                        else{
                            $sql_table_display = "SELECT * 
                                            FROM labs";
                        }
                        $result_table_display = mysqli_query($conn, $sql_table_display);
                        if(!$result_table_display){
                            echo "There is some problem in the connection.";
                            return;
                        }
                    }
                    while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
                    {    
                        ?>
                        <tr>
                            <form action="" method="post">
                                <input type="text" value="<?php echo $row['labno']?>" style="display:none" name="labno" id="labno">
                                <td><?php echo $row['labno']?></td>
                                <td class="lname"><?php echo $row['labname']?></td>
                                <td><?php echo $row['dept']?></td>
                                <td><?php echo $row['active'] ?></td>
                                <?php if($row['assistname']=='')
                                {
                                    $dept=$row['dept'];
                                    $sql_labassist_fetch = "SELECT * 
                                                            FROM user
                                                            WHERE role='lab-assistant' AND 
                                                                name NOT IN (SELECT assistname 
                                                                            from labs) AND 
                                                                status = 1 AND 
                                                                dept='$dept'";
                                    $result_labassist_fetch = mysqli_query($conn, $sql_labassist_fetch);
                                    if(!$result_labassist_fetch){
                                        echo "There was an error in fetching lab assistant names.";
                                        return;
                                    }
                                    ?>
                                    <td class="lname">
                                        <select id="assistant" name="assistant" required>
                                            <option value="0">None</option>';
                                            <?php 
                                                while($row = mysqli_fetch_array($result_labassist_fetch, MYSQLI_ASSOC)) {
                                            ?>
                                                <option value="<?php echo $row['id']?>"> <?php echo $row['name'] ?>  - <?php echo $row['id']?></option>
                                            <?php 
                                                } 
                                            ?>
                                        </select>
                                    </td>
                                <?php                
                                } else {
                                ?>
                                    <td> <?php echo $row['assistname'] ?> </td>
                                <?php
                                    }
                                ?>
                                <td>
                                    <button class="btn btn-outline-dark" type="submit" name="assist" style="width:150px;"> 
                                        <?php 
                                            if(!isset($row['assistname'])) echo 'Update'; else echo 'Remove'; 
                                        ?> Assistant 
                                    </button>
                                    <button class="btn btn-outline-danger" type="submit" name="lab">
                                        Delete Lab
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php
                    }
                ?>
                
            </tbody>
        </table>
    </div>
</div>
</body>
</html>