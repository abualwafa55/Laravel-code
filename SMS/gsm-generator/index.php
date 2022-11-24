<?php


$total = 1000;
$numbers_arr = [];

// $prefix = ['234802', '234803', '234805', '234806', '234807', '234808', '234809', '234810', '234811', '234812', '234813', '234814', '234815', '234816', '234817', '234818', '234702', '234703', '234704', '234705', '234706', '234707', '234708', '234709', '234902', '234903', '234905', '234906', '234907', '234908', '234909',];
// $pref1 = range(low, high)

$prefix = ['96650','96653','96654','96655','96656','96657','96658','96659'];
$start = date('Y-m-d H:i:s');
$file = fopen("new-contacts.txt", "w") or die("Unable to open file!");
$next_init = 5634567;
for ($counter = 0; $counter < $total; $counter++)
{
	$k = $counter + 1;
	// $next = rand(0000000, 9999999);
	$imsi = $prefix[rand(0, count($prefix) - 1)];
	$mobile_number = $imsi.$next_init;
	$next_init ++;

	$content = $mobile_number.",";
	//fwrite($file, $content);
	echo $k." Mobile Number -  ".MobileCodeName($imsi )." :".$mobile_number."<br /><br />";
	$firstName= MobileCodeName($imsi );
	$lastName = $k." Mobile Number";
	InsertContacts($mobile_number,$firstName,$lastName);
	//set_time_limit(20);
	$stop = date('Y-m-d H:i:s');
	//fclose($file);
}
	function MobileCodeName($prefix){
		if (in_array($prefix, array('96650', '96653', '96655') ) ){
			return 'STC-KSA';
		} elseif(in_array($prefix, array('96654', '96656'))) {
			return 'Mobily-KSA';
		}elseif(in_array($prefix, array('96658', '96659'))) {
			return 'Zain-KSA';
		}elseif($prefix == '96657') {
			return 'VirginMobile-KSA';
		}
	}
	 function InsertContacts($phoneNumber,$firstName,$lastName)
	{
		$group_id ="60a175dd50d95";
		$url = "http://sms.individual-software-solutions.com/api/v3/contacts/{$group_id}/store/";
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$headers = array(
		"Authorization: Bearer 2|osykjRUWVXs2LUdtKkR5srPH99F0xoMsSkoX73EE",
		"Content-Type: application/json",
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//$phoneNumber = "962777710033";
		//$firstName = "firstName";
		//$lastName= "lastName";
		$data = (object) array("phone"=>$phoneNumber,
		"first_name"=>$firstName,
		"last_name"=>$lastName
		);
		$fields = json_encode($data);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);

		//for debug only!
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$resp = curl_exec($curl);
		curl_close($curl);
		echo $phoneNumber .' inserted';
	//	var_dump($resp);
	}
?>