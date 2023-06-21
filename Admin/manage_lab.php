<?php 
    session_start();
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        include '../connection.php';
        $id=$_SESSION['id'];
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/dash_lab.php');    
		else if($role=='student')
			header('Location:../Student/dash_student.php');    
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="CSS/lab.css">
</head>
<body>
    <!-- TEMPORARY DASHBOARD -->
    <div style="width:450px;">
        <button onclick="window.location.href='manage_assist.php'"> 
            Manage Lab Assistants
        </button>
        <button onclick="window.location.href='manage_lab.php'">
            Manage Labs
        </button>
        <button onclick="window.location.href='../logout.php'">
            Signout
        </button>
    </div>
    <!-- TABLE DISPLAY  -->
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead>
                <tr>
                    <!-- HEADINGS -->
                    <th scope="col">Lab Num<br></th>
                    <th scope="col">Lab Name</th>
                    <th scope="col">Department</th>
                    <th scope="col">Status</th>
                    <th scope="col">Lab Assistant</th>
                    <th scope="col">Update<br></th>
                </tr>
            </thead>
            
            <tbody>
            <!-- EACH ROW IN WHILE LOOP DISPLAYING ALL LABS -->
            <?php
                $sql = mysqli_query($conn,"SELECT * FROM labs");
                $num = mysqli_num_rows($sql);
                while($row = mysqli_fetch_array($sql,MYSQLI_ASSOC)) 
                {    
                    ?>
                    <tr>
                        <form action="update_lab.php" method="post">
                        <input type="text" value="<?php echo $row['labno']?>" style="display:none" name="labno" id="labno">

                        <td><?php echo $row['labno']?></td>
                        <td><?php echo $row['labname']?></td>
                        <td><?php echo $row['dept']?></td>
                        <td><?php echo $row['active'] ?></td>
                        <?php if($row['assistname']=='')
                        {
                            $dept=$row['dept'];
                            $sql1=mysqli_query($conn,"SELECT * FROM user WHERE role='lab-assistant' AND name NOT IN (SELECT assistname from labs) AND status =1 AND dept='$dept'" );
                            ?>
                            <td>
                                <select id="assistant" name="assistant" required>
                                    <option value="none">None</option>';
                                    <?php while($row = mysqli_fetch_array($sql1,MYSQLI_ASSOC)) {
                                        ?>
                                    <option value="<?php echo $row['name']?>"> <?php echo $row['name'] ?>  - <?php echo $row['id']?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        <?php                
                        }
                        else 
                        {
                            ?>
                            <td> <?php echo $row['assistname'] ?> </td>
                            <?php
                        }
                        ?>
                        <td>
                            <button class="button1" type="submit" name="assist"> 
                                <?php if(!isset($row['assistname'])) echo 'Update'; else echo 'Remove'; ?> Assistant 
                            </button>
                                
                            <button class="button1" type="submit" name="lab">
                                Delete Lab
                            </button>
                            
                            </form>
                        </td>
                    
                    </tr>

                    <?php
                }
            ?>
            <tr>
            <form action="update_lab.php" method="post">
                <td><input type="text" name='labno' id='labno' required></td>
                <td><input type="text" name='labname' id='labname' required></td>
                <td><select id="dept" name="dept" required>
                    <option value="None">None</option>
                    <option value="EXTC">EXTC</option>
                    <option value="COMPS">COMPS</option>
                    </select></td>
                <td> <select id="active" name="active" required>
                    <option selected value="yes">Yes</option>
                    <option value="no">No</option>
                    </td>
                    <?php
                    // $sql1=mysqli_query($conn,"SELECT * FROM user WHERE role='lab-assistant' AND name NOT IN (SELECT assistname from labs) AND status =1" );
                    ?>
                    <td>
                    <i>(Assign assistant after creating lab)</i>
                        <!-- <select id="assistant" name="assistant" required>
                            <option value="none">None</option>';
                            <?php while($row = mysqli_fetch_array($sql1,MYSQLI_ASSOC)) {
                                ?>
                            <option value="<?php echo $row['name']?>"> <?php echo $row['name'] ?>  - <?php echo $row['id']?></option>
                            <?php } ?>
                        </select> -->
                    </td>
                
                <td>
                    <button class="button1" type="submit" name="addlab"> 
                        Create Lab
                    </button>
                </td>

            </tr>
            </tbody>
        </table>
    </div>
</body>
</html>