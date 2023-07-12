<?php
if (str_contains($_SERVER['REQUEST_URI'],'/Inventory_Management/Components/')){
  header('Location: ../login.php');
}
?>