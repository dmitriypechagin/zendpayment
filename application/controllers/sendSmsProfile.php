<?php
$src = '<?xml version="1.0" encoding="UTF-8"?>
			<SMS>
				<operations>
					<operation>SEND</operation>
				</operations>
				<authentification>
					<username>idealive36@gmail.com</username>
					<password>444970890</password>
				</authentification>
				<message>
					<sender>SMS</sender>
					<text>' . $_POST['code'] . '</text>
				</message>
				<numbers>
					<number messageID="msg11">' . $_POST['phone'] . '</number>
				</numbers>
			</SMS>';
 
    $Curl = curl_init();
    $CurlOptions = array(
        CURLOPT_URL=>'http://api.atompark.com/members/sms/xml.php',
        CURLOPT_FOLLOWLOCATION=>false,
        CURLOPT_POST=>true,
        CURLOPT_HEADER=>false,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_CONNECTTIMEOUT=>15,
        CURLOPT_TIMEOUT=>100,
        CURLOPT_POSTFIELDS=>array('XML'=>$src),
    );
    curl_setopt_array($Curl, $CurlOptions);
    if(false === ($Result = curl_exec($Curl))) {
        throw new Exception('Http request failed');
    }
 
    curl_close($Curl);
 
    echo $Result;
?>