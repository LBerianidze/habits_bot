<?php
if(isset($_GET['Login']) && isset($_GET['Password']))
{
    $UserName = $_GET['Login'];
    $password = $_GET['Password'];
    if($UserName=="Tieo")
    {
    	if($password=="Tieopass")
    	{
    		echo "Success";
    	}
    	else{
    		echo "Denied";
    	}
    }
    else
    {
    	echo "Denied";
    }
}
else
{
	echo "Denied";
}