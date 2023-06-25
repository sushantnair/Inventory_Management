<?php 
    session_start();
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        include '../connection.php';
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/dash.php');    
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <link rel="stylesheet" href="CSS/styles.css">
</head>
<body>
    <!-- TEMPORARY DASHBOARD -->
    <div>
        <button onclick="window.location.href='dash.php'"> 
            Dashboard
        </button>
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
    
    <form action="" method="post" style="text-align:center;">
        <input type="text" name="search" id="search" style="text-align:center;">
        <br>
        <label for="assigned">Lab Assistant Assigned?</label>
        <select id="assigned" name="assigned">
            <option value="">Any</option>
            <option value="and assistname!=''">Yes</option>
            <option value="and assistname=''">No</option>
        </select>
        <br>
        <label for="sta">Active</label>
        <select id="sta" name="sta">
            <option value="">Any</option>
            <option value="and active='yes'">Yes</option>
            <option value="and active='no'">No</option>
        </select>
        <br>
        <input type="submit" value="Search">
    </form>
    <?php 
        // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
        // if (isset($parts['query'])) 
        // {
        //     parse_str($parts['query'],$query);
        //     $search=$query['search'];
        //     $assign=$query['assigned'];
        //     $active=$query['sta'];
        //     echo $assign;
        //     echo $active;
        // }
        
    ?>
    <!-- TABLE DISPLAY  -->
    <div class="row col-lg-12 card card-body table-responsive">
        <table class="table table-centered table-nowrap mb-0">
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
                <!-- EACH ROW IN WHILE LOOP DISPLAYING ALL LABS -->
                <?php
                    // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
                    if (isset($_POST['search'])) 
                    {
                        // parse_str($parts['query'],$query);
                        $search=$_POST['search'];
                        $assign=$_POST['assigned'];
                        $active=$_POST['sta'];
                        $sql_table_display = "SELECT * 
                                            FROM labs 
                                            where (dept like '%$search%' OR 
                                                    labno like '%$search%' OR 
                                                    labname like '%$search%' OR 
                                                    assistname like '%$search%' ) 
                                            $assign $active";
                        $result_table_display = mysqli_query($conn, $sql_table_display);
                        if(!$result_table_display){
                            echo "There is some problem in the connection.";
                            return;
                        }
                    }
                    else 
                    {
                        $sql_table_display = "SELECT * 
                                            FROM labs";
                        $result_table_display = mysqli_query($conn, $sql_table_display);
                        if(!$result_table_display){
                            echo "There is some problem in the connection.";
                            return;
                        }
                    }
                    $num = mysqli_num_rows($result_table_display);
                    while($row = mysqli_fetch_array($result_table_display, MYSQLI_ASSOC)) 
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
                                    <td>
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
                                    <button class="button1" type="submit" name="assist"> 
                                        <?php 
                                            if(!isset($row['assistname'])) echo 'Update'; else echo 'Remove'; 
                                        ?> Assistant 
                                    </button>
                                    <button class="button1" type="submit" name="lab">
                                        Delete Lab
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php
                    }
                ?>
                <tr>
                    <form action="update_lab.php" method="post">
                        <td><input type="text" name='labno' id='labno' required></td>
                        <td><input type="text" name='labname' id='labname' required></td>
                        <td>
                            <select id="dept" name="dept" required>
                                <option value="None">None</option>
                                <option value="EXTC">EXTC</option>
                                <option value="COMPS">COMPS</option>
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
                            <button class="button1" type="submit" name="addlab"> 
                                Create Lab
                            </button>
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>