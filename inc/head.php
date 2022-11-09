<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes">
	<title>바른자세교정</title>
	<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

<!------------------ confirm & alert to modal ----------------------------------------->
<? 
//테스트중엔 true
$is_iphone = true;

if(stristr($_SERVER['HTTP_USER_AGENT'],'ipad') 
				|| stristr($_SERVER['HTTP_USER_AGENT'],'iphone') 
				|| strstr($_SERVER['HTTP_USER_AGENT'],'iphone') 
				|| stristr($_SERVER['HTTP_USER_AGENT'],'mac os')
				|| stristr($_SERVER['HTTP_USER_AGENT'],'macintosh')
				) {
	$is_iphone = true;
}

if($is_iphone) {
?>
	<style>
	#alert_msg_area { display: block; position: fixed; width: 100%; height:100%; z-index: 99999999; background:rgba(0,0,0,0.4);}
	#alert_msg_content { display: block; position: fixed; bottom: 50%; text-align: center; 
	width: 70%; background:#fff; padding:25px 0; left:0; right:0; margin:0 auto; border-radius:10px; box-shadow: 1px 1px 4px 1px #9e9e9e; }
	#alert_msg { color:#333; font-size:15px; display:block; text-indent: 5%;}
	#close_alert { display:inline-block; color:#fff;  width:70px; background:rgba(0,0,0,0.4); border-radius:30px; margin-top:15px; padding:5px; }

	#confirm_msg_area { display: block; position: fixed; width: 100%; height:100%; z-index: 99999999; background:rgba(0,0,0,0.4);}
	#confirm_msg_content { display: block; position: fixed; bottom: 50%; text-align: center; width: 60%; background:#fff; padding:25px 0; left:0; right:0; margin:0 auto; border-radius:10px; box-shadow: 1px 1px 4px 1px #9e9e9e; }
	#confirm_msg { color:#333; font-size:15px; display:block; }
	#confirm_yes, #confirm_no { display:inline-block; color:#fff; width:70px; background:rgba(0,0,0,0.4); border-radius:30px; margin-top:15px; padding:5px; }
	</style>
	<div id="alert_msg_area" style="display: none;">
		<div id="alert_msg_content">
			<p id="alert_msg"></p>
			<span id="close_alert" onclick="close_alert();">확인</span>
		</div>
	</div>
	<div id="confirm_msg_area" style="display: none;">
		<div id="confirm_msg_content">
			<p id="confirm_msg"></p>
			<span id="confirm_yes">확인</span>
			<span id="confirm_no">취소</span>
		</div>
	</div>
	<script>
	//alert

	function alert(msg) {
		$("#alert_msg").empty();
		$("#alert_msg").append(msg);
		$("#alert_msg_area").css("display","block");
	}

	function close_alert() {
		$("#alert_msg_area").css("display","none");
	}


	async function confirm(msg) {
		$("#confirm_msg").empty();
		$("#confirm_msg").append(msg);
		$("#confirm_msg_area").css("display","block");
		
		var result = await confrim_data();

		return result;
	}

	function confrim_data(){
		return new Promise(function(resolve, reject) {
			$("#confirm_yes").click(function(){
				$("#confirm_msg_area").css("display","none");
				resolve(true);
			});
			$("#confirm_no").click(function(){
				$("#confirm_msg_area").css("display","none");
				resolve(false);
			});	
		});
	}
	</script>
<?
}
?>
<!------------------ confirm & alert to modal end ----------------------------------------->

</head>
<body>
<!-- CONTENTS -->
<div id="app">