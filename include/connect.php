<?php

$con=mysqli_connect('localhost','root','','meowbrew');
if(!$con)
{
   die(mysqli_error($con));
   // echo "connected";
} 

?>