<?php
echo "test";die;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// read access details from configuration file
require('configuration.php');
$config = new JConfig;

//echo "<pre>";print_R($config);
// 1. connect to database

$con = mysqli_connect($config->host, $config->user, $config->password);


if (!$con) die('Could not connect: ' . mysql_error());
mysqli_select_db($con,$config->db);

$sql = "SELECT id,name,username,email from jos_users where block=0";


$result = mysqli_query($con,$sql);


	$totalCsvData = array();
        $header = array(
            'id',
            'Name',
            'Email',
            'Group',
        );  
        $totalCsvData[] = $header;
            while ($row = mysqli_fetch_array($result)) {

			$sql2 = "SELECT groups.title from jos_user_usergroup_map as users_groups INNER JOIN jos_usergroups as groups on groups.id=users_groups.group_id where users_groups.user_id=".$row['id'];
			
			$result2 = mysqli_query($con,$sql2);
			
			$prefix = $userList = '';
			
			while ($row2 = mysqli_fetch_array($result2)) {
				$val =$row2['title'];
				//echo "<pre>";print_R($row2);
				//echo $val";
				$userList .= $prefix . '"' . $val . '"';
   			        $prefix = ', ';
			}

                                  $data[] = array(
			             "id"=>$row['id'],
                                     "Name"=>trim($row['name']),
                                     "Email"=>$row['email'],
                                     "Group"=>$userList
                                 );
                         
             } 
        $data=  array_merge($totalCsvData,$data);
//echo "<pre>";print_R($data);die;


		header("Content-type: text/csv");
                header("Pragma: public");
                header("Cache-Control: private");
                header("Content-Disposition: attachment; filename=UserList.csv");
                header("Content-Description: PHP Generated Data");                                
                $out = fopen('php://output', 'w');
                foreach($data as $row){
                    fputcsv($out, $row);   
                }                
                fclose($out);  


