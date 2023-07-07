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
                mysqli_query($conn,"UPDATE user SET status=status+1 WHERE id='$assistid'");
            }
            else 
            {
                $fetch_id=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * from labs where labno='$labno'"));
                $assistid=$fetch_id['assistid'];
                mysqli_query($conn,"UPDATE labs SET assistid=0, assistname='' WHERE labno='$labno'");
                mysqli_query($conn,"UPDATE user SET status=status-1 WHERE id='$assistid'");

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
		else if($role=='User')
			header('Location:../User/index.php');    
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
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>
    <div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; z-index:100; width: calc(100% - 100px);">
    
        <form action="" method="post" style="text-align:center;">
            <br>
            <div class="row">
                <div class="col-sm-2">
                </div>
                <div class="col-sm-1 pe-0 mt-1">
                    <label for="search">Search</label>
                </div>
                <div class="col-sm-2 ps-0">
                    <input type="text" class="form-control" id="search" name="search">
                </div>
                <div class="col-sm-2 pe-0 mt-1">
                    <label for="assigned">Assistant Assigned?</label>
                </div>
                <div class="col-sm-1 ps-0">
                    <select id="assigned" name="assigned" class="form-select">
                        <option value="">Any</option>
                        <option value="and assistname!=''">Yes</option>
                        <option value="and assistname=''">No</option>
                    </select>           
                </div>
                <div class="col-sm-1 pe-0">
                    <input class="btn btn-outline-danger alert-danger" type="submit" value="Search"><br><br>
                </div>
            </div>
        </form>
        
    <!-- TABLE DISPLAY  -->
    <div class="row col-lg-12 card table-card card-body">
        <table class="mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th>Lab Num<br></th>
                    <th>Lab Name</th>
                    <th>Department</th>
                    <th>Active</th>
                    <th>Lab Assistant</th>
                    <th>Update<br></th>
                </tr>
            </thead>
            
            <tbody>
            <tr>
                    <form action="" method="post">
                        <td style="width:80px;"><input class="form-control" type="text" name='labno' id='labno' required></td>
                        <td><input class="form-control" type="text" name='labname' id='labname' required></td>
                        <td>
                            <select id="dept" name="dept" class="form-select" required style="text-align: center;">
                                <?php 
                                if($dept1!=NULL){
                                    ?>
                                    <option value="<?php echo $dept1; ?>"selected><?php echo $dept1; ?></option>
                                    <?php
                                }
                                else{
                                    ?>                                
                                    <option value="" disabled selected>None</option>
                                <?php
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
                            <select id="active" name="active" class="form-select" required style="text-align: center;">
                                <option selected value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </td>
                        <td>
                            <i>(After creating lab)</i>
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
                        // FOR LATER IF NEEDED
                        // $active=$_POST['sta'];
                        
                        if($dept1=='')
                        $sql_table_display = "SELECT * 
                                            FROM labs 
                                            where (labno like '%$search%' OR 
                                                    labname like '%$search%' OR 
                                                    dept like '%$search%' OR 
                                                    assistname like '%$search%') $assign  
                                            ";
                        else
                        $sql_table_display = "SELECT * 
                                            FROM labs 
                                            where dept='$dept1' AND (labno like '%$search%' OR 
                                                    labname like '%$search%' OR 
                                                    assistname like '%$search%') $assign  
                        ";
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
                                <td class="lname"><?php echo $row['dept']?></td>
                                <td><?php echo $row['active'] ?></td>
                                <?php if($row['assistname']=='')
                                {
                                    $dept=$row['dept'];
                                    $sql_labassist_fetch = "SELECT * 
                                                            FROM user
                                                            WHERE role='lab-assistant' AND 
                                                                (status = 1 OR status=2) AND 
                                                                dept='$dept'";
                                    $result_labassist_fetch = mysqli_query($conn, $sql_labassist_fetch);
                                    if(!$result_labassist_fetch){
                                        echo "There was an error in fetching lab assistant names.";
                                        return;
                                    }
                                    ?>
                                    <td class="lname">
                                        <select id="assistant" class="form-select" name="assistant" required style="text-align: center;">
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