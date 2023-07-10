<?php 
    session_start();
    $dept=$_SESSION['dept'];
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        include '../connection.php';
        $id=$_SESSION['id'];
        if(isset($_POST['btn-send'])){
            $tid=$_POST['tid'];
            $status=$_POST['btn-send'];
    
            if($status>=1){
                $ns=1;
            }
            elseif($status<=0){
                $ns=-1;
            }
    
            $sql = "UPDATE user SET status='$ns' WHERE id='$tid'";
            $result=mysqli_query($conn,$sql);
            header('Location:assist.php');
    
        }
    
        if(isset($_POST['delete'])){
            $tid=$_POST['tid'];
            $status=$_POST['status'];
    
            $sq="DELETE FROM user WHERE id='$tid'";
            mysqli_query($conn,$sq);
            header('Location:assist.php');
        }
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
		$role=$_SESSION['role'];
		if($role=='lab-assistant')
			header('Location:../LabAssistant/index.php');    
		else if($role=='user')
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
 <html>
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assist</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="../js/bootstrap.bundle.js"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" /><!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <!-- <link rel="stylesheet" href="../CSS/bootstrap.min.css"> -->
    <link rel="stylesheet" href="./CSS/styles.css">
    
</head>
<body style="background-color: #f8f9fc;overflow-x: hidden;">
    <?php include('../Components/sidebar.php') ?>     
 	<div class="position-absolute row pe-4 top-0 mx-4" style="left: 100px; width: calc(100% - 100px);">
        <form action="" method="post" style="text-align:center;">
            <br>
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-1 pe-0 mt-1">
                    <label for="search">Search</label>
                </div>
                <div class="col-md-2 ps-0">
                    <input type="text" class="form-control" id="search" name="search">
                </div>
                <div class="col-md-1 pe-0 mt-1">
                    <label for="sta" class="form-label">Status</label>
                </div>
                <div class="col-md-2 ps-0">
                    <select id="sta" name="sta" class="form-select">
                        <option value="">Any</option>
                        <option value="and status=1">Yes</option>
                        <option value="and status=0">No</option>
                    </select>            
                </div>
                <div class="col-md-1 pe-0">
                    <input class="btn btn-outline-danger alert-danger" type="submit" value="Search"><br><br>
                </div>
            </div>
        </form>
        <div class="row col-lg-12 card table-card card-body">
                    <!-- <div class="table-responsive"> -->
                        <table class="mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID Number<br></th>
                                    <th scope="col">Assistant Name</th>
                                    <th scope="col">Email Id</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Update<br></th>
                                </tr>
                            </thead>
                            <tbody>

                            
                            <?php
                                // For transactions in Home Page(index page)
                                // $parts = parse_url(basename($_SERVER['REQUEST_URI']));
                                if (isset($_POST['search'])) 
                                {
                                    // parse_str($parts['query'],$query);
                                    $search=$_POST['search'];
                                    $status=$_POST['sta'];
                                    if($dept!=NULL){
                                        $query_for_transactions="SELECT * FROM user WHERE role='lab-assistant' and dept='$dept' and (name like '%$search%' OR email like '%$search%' OR id like '%$search%') $status";
                                    }
                                    else{

                                    $query_for_transactions="SELECT * FROM user WHERE role='lab-assistant' and (name like '%$search%' OR email like '%$search%' OR id like '%$search%' OR dept like '%$search%') $status";
                                    }
                                }
                                else
                                {
                                    if($dept!=NULL){
                                        $query_for_transactions = "SELECT * FROM user WHERE role='lab-assistant' and dept='$dept'";
                                    }
                                    else{

                                        $query_for_transactions = "SELECT * FROM user WHERE role='lab-assistant'";
                                    }
                                }
                                $transaction_result = mysqli_query($conn,$query_for_transactions);
                                $no_of_transaction = mysqli_num_rows($transaction_result);

                                while($row = mysqli_fetch_array($transaction_result)) 
                                {
                                    $to_ID = $row['id'];
                                    $query_for_ben_name = "SELECT name FROM user WHERE id='$to_ID';";
                                    $result_ben_name = mysqli_query($conn, $query_for_ben_name);
                                    $ben_name = mysqli_fetch_array($result_ben_name)[0];
                                    
                                    if($row['status']>=1){
                                        $sta="Granted";
                                    }
                                    elseif($row['status']==0){
                                        $sta="Pending";
                                    }
                                    elseif($row['status']==-1){
                                        $sta="Revoked";
                                    }
                                    ?>
                                        <tr>          
                                        <form action="" method="post">     
                                            <input name="tid" style="display:none;" type="text" value="<?php echo $row['id']?>">
                                            <td>
                                               <?php echo $row['id']; ?>
                                            </td>
                                            <td class="lname">
                                               <?php echo $ben_name; ?> 
                                            </td>
                                            <td class="lname">
                                           <?php echo $row['email']; ?>
                                            </td>
                                            <td class="lname">
                                           <?php echo $row['dept']; ?>
                                            </td>
                                            <td>
                                               <?php echo $sta; ?> 
                                            </td>
                                    
                                            
                                            <td>
                                            <button type="button" name="return" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#staticBackdropAsstupdate<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['id']));?>">
                                                <?php 
                                                if($row['status']<=0)
                                                {
                                                    ?>
                                                        Grant Access
                                                        <!-- <button class="btn btn-outline-dark" type="submit" name="btn-send" value="1">Grant Access</button> -->
                                                    <?php 
                                                }
                                                else if($row['status']>=1) 
                                                {
                                                    ?>
                                                        <!-- <button class="btn btn-outline-dark" type="submit" name="btn-send" value="-1">Revoke Access</button> -->
                                                        Revoke Access
                                                    <?php 
                                                } 
                                                ?>
                                            </button>
                                            <button type="button" name="return" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#staticBackdropAsstdelete<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['id']));?>">
                                                Delete Account
                                            </button>
                                            

                                                
                                            </td>
                                            <div class="modal fade" id="staticBackdropAsstupdate<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['id']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="staticBackdropLabel">Update Lab Asst.</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                                $lab_id=$row['id'];
                                                                if($row['status']<=0)
                                                                    echo "Are you sure you want to grant access to the following Lab Assistant?<br>";
                                                                else if($row['status']>=1)
                                                                    echo "Are you sure you want to revoke access from the following Lab Assistant?<br>";
                                                                echo "Name: <strong>".$ben_name."</strong><br>";
                                                                echo "Email: <strong>".$row['email']."</strong><br>";
                                                                echo "Dept: <strong>".$row['dept']."</strong><br><br>";
                                                                
                                                            ?>
                                                                <?php if($row['status']<=0){ ?>
                                                                <p style="font-size: x-small; margin:0;">Clicking 'Grant Access' will grant this user Lab Assistant privileges.</p>
                                                                <?php } else if($row['status']>=1) { ?>
                                                                <p style="font-size: x-small; margin:0;">Clicking 'Revoke Access' will revoke Lab Assistant privileges from this user.</p>
                                                                <?php } ?>
                                                                <p style="font-size: x-small;">Click 'Cancel' to dismiss the popup for now.</p>
                                                        </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn alert-danger" data-bs-dismiss="modal">No, Cancel</button>
                                                        <?php 
                                                        if($row['status']<=0)
                                                        {
                                                            ?>
                                                                
                                                                <button class="btn btn-outline-dark" type="submit" name="btn-send" value="1">Yes, Grant Access</button>
                                                            <?php 
                                                        }
                                                        else if($row['status']>=1) 
                                                        {
                                                            ?>
                                                                <button class="btn btn-outline-dark" type="submit" name="btn-send" value="-1">Yes, Revoke Access</button>
                                                                
                                                            <?php 
                                                        } 
                                                        ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="staticBackdropAsstdelete<?php echo str_replace(array('/','(',')'), array('_','open','close'), strtolower($row['id']));?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="staticBackdropLabel">Delete Lab Asst.</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                                $lab_id=$row['id'];
                                                                echo "Are you sure you want to delete the following Lab Assistant?<br>";
                                                                echo "Name: <strong>".$ben_name."</strong><br>";
                                                                echo "Email: <strong>".$row['email']."</strong><br>";
                                                                echo "Dept: <strong>".$row['dept']."</strong><br><br>";
                                                                
                                                            ?>
                                                                <p style="font-size: x-small; margin:0;">This action cannot be reversed!</p>
                                                        </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn alert-danger" data-bs-dismiss="modal">No, Cancel</button>
                                                        <button class="btn btn-outline-danger" type="submit" name="delete">Delete Account</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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