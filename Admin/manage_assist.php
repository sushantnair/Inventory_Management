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

     

    if(isset($_POST['btn-send'])){
        $id=$_POST['tid'];
        $status=$_POST['status'];

        if($status=="approved"){
            $ns=1;
        }
        elseif($status=="revoked"){
            $ns=0;
        }

        $sql = "UPDATE user SET status='$ns' WHERE id='$id'";
        $result=mysqli_query($conn,$sql);
    }

    if(isset($_POST['delete'])){
        $id=$_POST['tid'];
        $status=$_POST['status'];

        $sq="DELETE FROM user WHERE id='$id'";
        mysqli_query($conn,$sq);
    }


?>
 <!DOCTYPE html>
 <html>
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="CSS/lab.css">
</head>
 <body>
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

<div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID Number<br></th>
                                    <th scope="col">Assistant Name</th>
                                    <th scope="col">Email Id</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Options</th>
                                    <th scope="col">Update<br></th>
                                </tr>
                            </thead>
                            <tbody>

                            
                            <?php
                                // For transactions in Home Page(index page)
                                $query_for_transactions = "SELECT * FROM user WHERE role='lab-assistant'";
                                $transaction_result = mysqli_query($conn,$query_for_transactions);
                                $no_of_transaction = mysqli_num_rows($transaction_result);

                                while($row = mysqli_fetch_array($transaction_result)) 
                                {
                                    $to_ID = $row['id'];
                                    $query_for_ben_name = "SELECT name FROM user WHERE id='$to_ID';";
                                    $result_ben_name = mysqli_query($conn, $query_for_ben_name);
                                    $ben_name = mysqli_fetch_array($result_ben_name)[0];
                                    
                                    if($row['status']==1){
                                        $sta="Granted";
                                    }
                                    elseif($row['status']==0){
                                        $sta="Pending";
                                    }
                                    echo 
                                        '<tr>          
                                        <form action="" method="post">     
                                            <input name="tid" style="display:none;" type="text" value='.$row['id'].' />
                                            <td>
                                                '.$row['id'].'
                                            </td>
                                            <td>
                                                '.$ben_name.' 
                                            </td>
                                            <td>
                                            '.$row['email'].'
                                            </td>
                                            <td>
                                            '.$row['dept'].'
                                            </td>
                                            <td>
                                                '.$sta.' 
                                            </td>
                                            <td>
                                                <select id="status" name="status" required>
                                                    <option value="default">None</option>
                                                    <option value="approved">Grant Access</option>
                                                    <option value="revoked">Revoke Access</option>
                                                </select>
                                            </td>
                                            
                                            <td>
                                                <button class="button1" type="submit" name="btn-send">Update</button>
                                                <button class="button1" type="submit" name="delete">Delete</button>
                                                </form>
                                            </td>
                                            
                                        </tr>
                                    ';
                                } 
                                
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    </body>
    </html>