<?php 
    session_start();
    include('../connection.php');
    //If a user is logged in and is an admin
    if (isset($_SESSION['logged']) && $_SESSION['role']=='admin') 
    {
        $id=$_SESSION['id'];
    }
    //If a user is logged in and is not an admin
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='admin')
    {
        include 'connection.php';
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
    <style type="text/css">
        h1{
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }
    h2{
      color: red;
      text-align: center;
    }
    th{
        text-align:center;
    }
    button{
        background-color: #4CAF50;
	color: white;
	padding: 5px 20px;
    margin-right:10px;
	border: none;   
	border-radius: 4px;
	cursor: pointer;

    }
    </style>
 </head>
 <body>
<div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
           
                                        <div class="table-responsive">
                                            <table class="table table-centered table-nowrap mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">ID<br></th>
                                                        <th scope="col">Account Name</th>

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

                                                    while($row = mysqli_fetch_array($transaction_result)) {
                                                        $to_ID = $row['id'];
                                                        $query_for_ben_name = "SELECT fname FROM user WHERE id='$to_ID';";
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
                                                            <td style="text-align:center">
                                                                <div class="avatar-xs" style="background-color: white;" >
                                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary" style="background-color: white;">
                                                                    <input name="tid" type="text"  style="background-color: white; border-color:white; text-align:center; " readonly="readonly" value='.$row['id'].' />
                                                                        
                                                                    </span>&nbsp;
                                                                </div>
                                                            </td>
                                                            <td style="text-align:center">
                                                                <h4 class="font-size-15 mb-0">'.$ben_name.' </h3>
                                                            </td>
                                                            <td style="text-align:center">
                                                                <h5 class="font-size-15 mb-0">'.$sta.' </h3>
                                                            </td>
                                                            <td style="text-align:center"><select id="status" name="status" required>
                                                            <option value="default">None</option>
                                                            <option value="approved">Grant Access</option>
                                                            <option value="revoked">Revoke Access</option>
                                                          </select></td>
                                                            <td style="text-align:center"><div class="btttn"><button type="submit" name="btn-send">Update</button><button type="submit" name="delete">Delete</button></div></form>
                                                            <div class="col-sm-4"><br></td>
                                                            
                                                    </tr>';
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