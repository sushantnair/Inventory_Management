<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        include '../connection.php';
        $id=$_SESSION['id'];
        if(isset($_POST['lend']))
        {
            $dsrno=$_POST['dsrno'];
            $labno=$_POST['labno'];

            $fetch_equipment=mysqli_query($conn,"SELECT * FROM $labno WHERE dsrno='$dsrno'");
            if(!$fetch_equipment)
            {
                echo mysqli_error($conn);
                die();
            }
            else
            {
                $eqrow=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
                $eqtype=$eqrow['eqtype'];
                $eqname=$eqrow['eqname'];
                $quantity=$eqrow['quantity'];
                $desc1=$eqrow['desc1'];
                $desc2=$eqrow['desc2'];
                $cost=$eqrow['cost'];
            }


        }
        else if(isset($_POST['lending']))
        {

            $dsrno=$_POST['dsrno']; 
            $lendfrom=$_POST['labno'];  //LEND FROM
            $lendquan=$_POST['lendquan'];   //LENDING QUANTITY
            $lendto=$_POST['lendid']; //LEND TO
            if($lendto=='0')
                header("Location:view_equ.php");

            $fetch_equipment=mysqli_query($conn,"SELECT * FROM $lendfrom WHERE dsrno='$dsrno'");
            
            //FETCH EQUIPMENT DETAILS
            $eqrow=mysqli_fetch_array($fetch_equipment,MYSQLI_ASSOC);
            
            //STORE EQUIPMENT DETAILS
            $eqname=$eqrow['eqname'];
            $eqtype=$eqrow['eqtype'];
            $quantity=$eqrow['quantity'];
            $desc1=$eqrow['desc1'];
            $desc2=$eqrow['desc2'];
            $cost=$eqrow['cost'];
            
            
            $check_ownership=mysqli_query($conn,"SELECT * FROM lend WHERE dsrno='$dsrno' AND lendto='$lendfrom'");

            if(mysqli_num_rows($check_ownership)==1)
            {
                $fetch_owner=mysqli_fetch_array($check_ownership,MYSQLI_ASSOC);
                $orignal_labno=$fetch_owner['lendfrom'];
                $orignal_lend_quan=$fetch_owner['lendquan'];


                if($orignal_lend_quan==$lendquan)   //ALL EQUIPMENTS BEING LEND FROM LAB-B TO LAB-C (OWNED BY LAB-A)
                {
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    if(mysqli_num_rows($check_prev_lend)==0)    //LAB-C NOT LENT SAME EQUIPMENT FROM LAB-A
                    {
                        //SHIFT 'lend' TRANSACTION 'lendto' FROM LAB-B TO LAB-C
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendto='$lendto' WHERE lendto='$lendfrom' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                        $create_reciever_quantity=mysqli_query($conn,"INSERT INTO $lendto (eqname,dsrno,quantity,byquan,eqtype,desc1,desc2,cost) values('$eqname','$dsrno',$lendquan,$lendquan,'$eqtype','$desc1','$desc2',$cost)");
                    }
                    else    //LAB-C PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
                    {
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                        $delete_old_transaction=mysqli_query($conn,"DELETE FROM lend WHERE lendto='$lendfrom' AND dsrno='$dsrno'");
                        $update_reciever_quantity=mysqli_query($conn,"UPDATE $lendto SET byquan=(byquan+$lendquan),quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");
                        
                    }
                        //DELETE FROM LAB-B
                    $remove_old_lend=mysqli_query($conn,"DELETE FROM $lendfrom WHERE dsrno='$dsrno'");

                        
                }
                else    //SOME EQUIPMENTS BEING LEND TO LAB-C
                {
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                    if(mysqli_num_rows($check_prev_lend)==0)    //LAB-C NOT LENT SAME EQUIPMENT FROM LAB-A
                    {                        
                        $insert_transaction=mysqli_query($conn,"INSERT INTO lend(lendfrom,dsrno,lendquan,lendto) values('$orignal_labno','$dsrno',$lendquan,'$lendto')");
                        $modify_old=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan-$lendquan) WHERE lendto='$lendfrom' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                        $create_reciever_quantity=mysqli_query($conn,"INSERT INTO $lendto (eqname,dsrno,quantity,byquan,eqtype,desc1,desc2,cost) values('$eqname','$dsrno',$lendquan,$lendquan,'$eqtype','$desc1','$desc2',$cost)");

                    }                    
                    else    //LAB-C PREVIOUSLY LENT SAME EQUIPMENT FROM LAB-A
                    {
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                        $modify_old=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan-$lendquan) WHERE lendto='$lendfrom' AND dsrno='$dsrno' AND lendfrom='$orignal_labno'");
                        $update_reciever_quantity=mysqli_query($conn,"UPDATE $lendto SET byquan=(byquan+$lendquan),quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");

                    }
                    //SUBTRACT FROM LAB-B
                    $update_old_lend=mysqli_query($conn,"UPDATE $lendfrom SET quantity=quantity-$lendquan, byquan=byquan-$lendquan WHERE dsrno='$dsrno'");

                
                }
            }
            else 
            {
                    //INSERT TRANSACTION IN LEND TABLE
                    $check_prev_lend=mysqli_query($conn,"SELECT * FROM lend WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom'");
                    if(mysqli_num_rows($check_prev_lend)==0)
                        $insert_transaction=mysqli_query($conn,"INSERT into lend(lendfrom,dsrno,lendquan,lendto) VALUES('$lendfrom','$dsrno',$lendquan,'$lendto')");
                    else
                        $insert_transaction=mysqli_query($conn,"UPDATE lend SET lendquan=(lendquan+$lendquan) WHERE lendto='$lendto' AND dsrno='$dsrno' AND lendfrom='$lendfrom'");
                    if(!$insert_transaction)
                    {
                        echo mysqli_error($conn);
                        die();
                    }
                    else
                    {
                        //UPDATE LENDER QUANTITIES
                        $update_lender_quantity=mysqli_query($conn,"UPDATE $lendfrom SET toquan=(toquan+$lendquan), quantity=(quantity-$lendquan) WHERE eqname='$eqname' AND dsrno='$dsrno'");
                        if(!$update_lender_quantity)
                        {
                            echo mysqli_error($conn);
                            die();
                        }
                        else
                        {
                            //CHECKING IF RECIEVING LAB HAS PREVIOUSLY LENT SAME PRODUCT 
                            $product_present=mysqli_query($conn,"SELECT * FROM $lendto WHERE eqname='$eqname' AND dsrno='$dsrno'");
                            if(!$product_present)
                            {
                                echo mysqli_error($conn);
                                die();
                            }
                            else
                            {
                            
                                if(mysqli_num_rows($product_present)==0)
                                {
                                    //IF NOT LENDING SAME PRODUCT AGAIN
                                    $create_reciever_quantity=mysqli_query($conn,"INSERT INTO $lendto (eqname,dsrno,quantity,byquan,eqtype,desc1,desc2,cost) values('$eqname','$dsrno',$lendquan,$lendquan,'$eqtype','$desc1','$desc2',$cost)");
                                    if(!$create_reciever_quantity)
                                    {
                                        echo mysqli_error($conn);
                                        die();
                                    }
                                }
                                else
                                {
                                    //IF LENDING SAME PRODUCT AGAIN
                                    $update_reciever_quantity=mysqli_query($conn,"UPDATE $lendto SET byquan=(byquan+$lendquan),quantity=(quantity+$lendquan) WHERE dsrno='$dsrno'");
                                    if(!$update_lender_quantity)
                                    {
                                        echo mysqli_error($conn);
                                        die();
                                    }
                                }
                            }
                            
                        }
                        
                    
                    // header("Location:view_equ.php");
                    
                
                    }
            }

            

            
        }
        
    }
    //If a user is logged in and is a not lab-assistant
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">-->
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <!-- using an offline copy saves time spent for loading bootstrap from online source  -->
    <title>IM-KJSCE</title>
</head>
<body>
    
    <div>
        <button onclick="window.location.href='dash.php'"> 
            Dashboard
        </button>
        <button onclick="window.location.href='view_equ.php'"> 
            View Equipment
        </button>
        <button onclick="window.location.href='lent_equ.php'"> 
            Lend Equipment
        </button>
        <button onclick="window.location.href='../logout.php'"> 
            Sign Out
        </button>        
    </div>
    <form action="lend.php" method="post" style="text-align:center">
        <br>
        <input type="text" name="eqname" value="<?php echo $eqname; ?>" style="display:none;">
        <input type="text" name="dsrno" value="<?php echo $dsrno; ?>" style="display:none;">
        <input type="text" name="labno" value="<?php echo $labno; ?>" style="display:none;">
           
        <?php 
            echo "Name: ".$eqname."<br>";
            echo "DSR: ".$dsrno."<br>";
            echo "Quantity: ".$quantity."<br>";
        ?>
        <br>
        
        <label for="lendid">Lend ID</label>
        <select type="number" name="lendid" id="lendid">
            <option value="0">None</option>
            <?php
                $fetch_labs=mysqli_query($conn,"SELECT * FROM labs WHERE NOT labno='$labno' AND labno NOT IN (SELECT lendfrom FROM lend WHERE dsrno='$dsrno' AND lendto='$labno')");
                while($lab_row=mysqli_fetch_array($fetch_labs,MYSQLI_ASSOC))
                {
                    $labname=$lab_row['labname'];
                    $labnum=$lab_row['labno'];
                    ?>
                    <option value="<?php echo $labnum;?>" ><?php echo $labnum;?> - <?php echo $labname;?></option>
                    <?php
                }
            ?>
            
        </select>
        <br>
        <label for="lendto">Lend Quantity</label>
        <input type="number" name="lendquan" id="lendquan" min ="1" max="<?php echo $quantity;?>" style="width:150px;" required>
        <br>
        <button class="button1" type="submit" name="lending"> 
            Lend
        </button>
    </form>
</body>
</html>