<?php 
    session_start();
    //If a user is logged in and is a lab-assistant
    if (isset($_SESSION['logged']) && $_SESSION['role']=='lab-assistant') 
    {
        include '../connection.php';
        $id=$_SESSION['id'];
        $status=$_SESSION['status'];
        $labno=$_SESSION['labno'];

        if($labno==$_SESSION['labno2'])
            $lab2=$_SESSION['labno1'];
        else
            $lab2=$_SESSION['labno2'];

        $labsql=mysqli_query($conn,"SELECT * FROM labs WHERE labno='$labno'");
        $labrow = mysqli_fetch_array($labsql,MYSQLI_ASSOC);
        $labname=$labrow['labname'];
        $labdept=$labrow['dept'];

        $assistsql=mysqli_query($conn,"SELECT * FROM user WHERE id=$id");
        $assistrow=mysqli_fetch_array($assistsql,MYSQLI_ASSOC);
        $assistname=$assistrow['name'];
        $assistemail=$assistrow['email'];

        $filename = $labno.".csv";
        $fp = fopen($filename, 'w');

        fputcsv($fp,array("Inventory Report"));
        fputcsv($fp,array("\r\n"));

        fputcsv($fp,array("Lab Details:"));
        fputcsv($fp,array("Lab No: $labno","Lab Name: $labname","Department: $labdept"));
        fputcsv($fp,array("Lab Assistant Details:"));
        fputcsv($fp,array("ID: $id","Name: $assistname","Email: $assistemail"));
        fputcsv($fp,array("\r\n"));

        fputcsv($fp,array("Current Equipment Held:","(Excluding Borrowed)"));
        $sql1=mysqli_query($conn,"SELECT * FROM $labno WHERE byquan=0");
        fputcsv($fp,array("Equipment Name","DSR No.","Equipment Type", "Quantity", "Lent Quantity","Description 1", "Description 2", "Cost"));
        while($row = mysqli_fetch_assoc($sql1))
        {
            fputcsv($fp, array($row['eqname'],$row['dsrno'],$row['eqtype'],$row['quantity'],$row['toquan'],$row['desc1'],$row['desc2'],$row['cost']));
        }
        fputcsv($fp,array("\r\n"));

        fputcsv($fp,array("Borrowed Equipment:"));
        $sql2=mysqli_query($conn,"SELECT * FROM lend JOIN $labno USING (dsrno) WHERE lendto='$labno'");
        fputcsv($fp,array("Equipment Name","DSR No.","Equipment Type", "Borrowed Quantity","Description 1", "Description 2","Borrowed From"));
        while($row = mysqli_fetch_assoc($sql2))
        {
            fputcsv($fp, array($row['eqname'],$row['dsrno'],$row['eqtype'],$row['lendquan'],$row['desc1'],$row['desc2'],$row['lendfrom']));
        }
        fputcsv($fp,array("\r\n"));

        fputcsv($fp,array("Lent Equipment:"));
        $sql2=mysqli_query($conn,"SELECT * FROM lend JOIN $labno USING (dsrno) WHERE lendfrom='$labno'");
        fputcsv($fp,array("Equipment Name","DSR No.","Equipment Type","Description 1", "Description 2","Lent to", "Lent Quantity"));
        while($row = mysqli_fetch_assoc($sql2))
        {
            fputcsv($fp, array($row['eqname'],$row['dsrno'],$row['eqtype'],$row['desc1'],$row['desc2'],$row['lendto'],$row['lendquan']));
        }
        fputcsv($fp,array("\r\n"));

        fputcsv($fp,array("Equipment Requests:"));
        $sql2=mysqli_query($conn,"SELECT * FROM request JOIN $labno USING (dsrno) WHERE labno='$labno'");
        fputcsv($fp,array("Equipment Name","DSR No.","Equipment Type","Description 1", "Description 2","Request From", "Request Quantity"));
        while($row = mysqli_fetch_assoc($sql2))
        {
            fputcsv($fp, array($row['eqname'],$row['dsrno'],$row['eqtype'],$row['desc1'],$row['desc2'],$row['id'],$row['requan']));
        }
        // fputcsv($fp,array("\r\n"));


        fclose($fp);
        header('Content-type: text/csv');
        header('Content-disposition:attachment; filename="'.$filename.'"');
        readfile($filename);
        unlink($filename);
        // header('Location:index.php');

    }
    //If a user is logged in and is not a lab-assistant
    else if (isset($_SESSION['logged']) && $_SESSION['role']!='lab-assistant')
    {
		$role=$_SESSION['role'];
		if($role=='admin')
			header('Location:../Admin/index.php');    
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