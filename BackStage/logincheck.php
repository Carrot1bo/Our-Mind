<?php  
	error_reporting(E_ALL ^ E_DEPRECATED);
	session_start();
	
    if(isset($_POST["submit"]) && $_POST["submit"] == "登陆")  
    {  
        $user = $_POST["username"];  
        $psw = $_POST["password"];  
        if($user == "" || $psw == "")  
        {  
            echo "<script>alert('请输入用户名或密码！'); history.go(-1);</script>";  
        }  
        else  
        {  
            mysql_connect("localhost","root","08");  
            mysql_select_db("our-mind");  
            mysql_query("set names 'gbk'");  
            $sql = "select UserID,Password from user where UserID = '$_POST[username]' and Password = '$_POST[password]'";  
            $result = mysql_query($sql);  
            $num = mysql_num_rows($result);  
            if($num)  
            {  
				$row = mysql_fetch_array($result);  //将数据以索引方式储存在数组中
				
				$_SESSION['id'] = $row[0];
				$_SESSION['key'] = $row[1];
				
				header("Location:Userpage.php"); 
				  
                //echo $row[0];  
            }  
            else  
            {  
                echo "<script>alert('用户名或密码不正确！');history.go(-1);</script>";  
            }  
        }  
    }  
    else  
    {  
        echo "<script>alert('提交未成功！'); history.go(-1);</script>";  
    }  
  
?>  