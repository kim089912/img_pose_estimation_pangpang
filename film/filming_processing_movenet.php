<? include "../../inc/head.php"; ?>
<script>
	function alertback(str) {
		alert(str);
		$("#close_alert").click(function(){
			location.href = "./filming.php";
		});		
	}
</script>
<?php
    $front_file = $_FILES['front_img']['name'];
    $side_file = $_FILES['side_img']['name'];

    //파일명에 특수문자 제거 (업로드에 문제 생김 종종)
    $front_file_name = str_replace(array("#", "'", ";","[","]","(",")","*"), '', $front_file);
    $side_file_name = str_replace(array("#", "'", ";","[","]","(",")","*"), '', $side_file);
    
    //파일 존재 백엔드 유효성검사
    if(($front_file_name == "")||($side_file_name == "")){
	?>   
        <script>
			alertback('두개의 이미지 파일을 첨부해야 합니다.');
		</script>	
	<?
    }
    
    //파일 jpg,png 확인 유효성 검사
    $front_fp = fopen($_FILES["front_img"]["tmp_name"], "r");
    $side_fp = fopen($_FILES["side_img"]["tmp_name"], "r");
    $front_image_stream = fread($front_fp, 64);
    $side_image_stream = fread($side_fp, 64);
    if ((!preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $front_image_stream)&&!preg_match( '/^\xff\xd8/', $front_image_stream))&&(!preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $side_image_stream)&&!preg_match( '/^\xff\xd8/', $side_image_stream))){
	?>   
        <script>
			alertback('jpg/png 이미지 파일만 업로드 가능합니다.');
		</script>	
	<?
    }
    
    //이미지 파일 담을 디렉토리와 파일명 변수 지정
    if(preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $front_image_stream)){
        $front_file_type = "png";
        $front_base_name = basename($front_file_name,".png");
    } else if(preg_match( '/^\xff\xd8/', $front_image_stream)){
        $front_file_type = "jpg";
        $front_base_name = basename($front_file_name,".jpg");
    }
    if(preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $side_image_stream)){
        $side_file_type = "png";
        $side_base_name = basename($side_file_name,".png");
    } else if(preg_match( '/^\xff\xd8/', $side_image_stream)){
        $side_file_type = "jpg";
        $side_base_name = basename($side_file_name,".jpg");
    }
    
    $ndate = date("Y-m-d_H-i-s");
    $save_front_front_directory = "../../img/pose/front/";
    $save_side_front_directory = "../../img/pose/side/";

    //$save_front_back_directory = $front_base_name."_".$ndate."/";
    //$save_side_back_directory = $side_base_name."_".$ndate."/";

    if($front_file_type == 'png'){
        $save_front_file_name = $front_base_name."_".$ndate.".png";
    } else if($front_file_type == 'jpg'){
        $save_front_file_name = $front_base_name."_".$ndate.".jpg";
    }
    if($side_file_type == 'png'){
        $save_side_file_name = $side_base_name."_".$ndate.".png";
    } else if($side_file_type == 'jpg'){
        $save_side_file_name = $side_base_name."_".$ndate.".jpg";
    }
	$front_target_file = $save_front_front_directory.$save_front_file_name;
    $side_target_file = $save_side_front_directory.$save_side_file_name;

    //$front_target_file = $save_front_front_directory.$save_front_back_directory.$save_front_file_name;
    //$side_target_file = $save_side_front_directory.$save_side_back_directory.$save_side_file_name;

	/*
    //이미지 파일 담을 디렉토리 생성
    //$front_makeDir = mkdir($save_front_front_directory.$save_front_back_directory, '777');
    //$side_makeDir = mkdir($save_side_front_directory.$save_side_back_directory, '777');
    $front_makeDir = mkdir($save_front_front_directory, '777');
    $side_makeDir = mkdir($save_side_front_directory, '777');
	$shell_code = 'sudo chmod 777 '.$save_front_front_directory.$save_front_back_directory;
	echo shell_exec($shell_code);
	$shell_code = 'sudo chmod 777 '.$save_side_front_directory.$save_side_back_directory;
	echo shell_exec($shell_code);
	
    if(!$front_makeDir||!$side_makeDir){
        echo "<script>alert('이미지 파일 폴더 생성에 오류가 있습니다.');</script>";
        echo "<script>location.href='./filming.php'</script>";		
    }
	*/

    //이미지 파일 업로드
    if ((move_uploaded_file($_FILES["front_img"]["tmp_name"], $front_target_file))&&(move_uploaded_file($_FILES["side_img"]["tmp_name"], $side_target_file))) {
        
    } else {
	?>   
        <script>	
			alertback('이미지 파일 업로드에 오류가 있습니다.');
		</script>	
	<?
    }

	//이미지 사이즈 구하기 ( 이유 : 텐서플로우 js 는 이미지의 width height 를 지정해주지 않으면 invaild 에러날 수도 있음 )
	$front_img_size = getimagesize($front_target_file);
	$side_img_size = getimagesize($side_target_file);

	//이미지 크기 줄이기 최대 너비 : 변수로 지정 ( 텐서플로우 js 가 픽셀로 이미지를 불러오는데 너무 크면 그만큼 서버 gpu 를 잡아 먹음 )
	//( 그렇다고 너무 작으면 사람 인식이 힘듬 )
	$max_width = 600;
	$front_img_prop = $front_img_size[1]/$front_img_size[0];
	$side_img_prop = $side_img_size[1]/$side_img_size[0];
	$front_img_prop2 = $front_img_size[0]/$front_img_size[1];
	$side_img_prop2 = $side_img_size[0]/$side_img_size[1];

	$front_img_width = $front_img_size[0];
	$front_img_height = $front_img_size[1];
	$side_img_width = $side_img_size[0];
	$side_img_height = $side_img_size[1];

	if($front_img_width > $max_width){
		$front_img_width = $max_width;
		$front_img_height = $max_width*$front_img_prop;
	}
	if($side_img_width > $max_width){
		$side_img_width = $max_width;
		$side_img_height = $max_width*$side_img_prop;
	}
	if($front_img_height > $max_width){
		$front_img_height = $max_width;
		$front_img_width = $front_img_height*$front_img_prop2;
	}
	if($side_img_height > $max_width){
		$side_img_height = $max_width;
		$side_img_width = $side_img_height*$side_img_prop2;
	}

?>
<head>
	<!-- Require the peer dependencies of pose-detection. -->
	<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-core"></script>
	<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-converter"></script>

	<!-- You must explicitly require a TF.js backend if you're not using the TF.js union bundle. -->
	<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>
	<!-- Alternatively you can use the WASM backend: <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-wasm/dist/tf-backend-wasm.js"></script> -->

	<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/pose-detection"></script>
	<style>
		/* 이미지에 캔버스를 겹쳐서 그릴때는 아래 주석 해제 */
		#front_canvas,#side_canvas {
			position: absolute;
			top: 0;
			left: 0;
		}
		#front_canvas_s,#side_canvas_s {
			position: absolute;
			top: 0;
			left: 0;
		}
		#front_img_div,#side_img_div {
			position:  relative;
		}
	</style>
</head>

<body>
	<div id="front_img_div">
		<img class='pose_img' style='width:<?=$front_img_width?>px; height:<?=$front_img_height?>px; display:none;' id='front' src='<?=$front_target_file?>' onLoad="front_pose_load();"/>
		<img id='front_s'/>
		<canvas id="front_canvas" style='display:none;'></canvas>
		<canvas id="front_canvas_s"></canvas>
	</div>
	<div id="side_img_div">
		<img class='pose_img' style='width:<?=$side_img_width?>px; height:<?=$side_img_height?>px; display:none;' id='side' src='<?=$side_target_file?>' onLoad="side_pose_load();"/>
		<img id='side_s'/>
		<canvas id="side_canvas" style='display:none;'></canvas>
		<canvas id="side_canvas_s"></canvas>
	</div>
</body>
<script>

	/*************************** 키포인트 산출 및 스켈레톤 그리기 시작 ***************************/

	// 선 그리기 메소드 (movenet 은 이미지용 스켈레톤 그리기 메소드가 별도로 없다)
	function draw(poses,canvas) {

        canvas.beginPath();
		canvas.strokeStyle = 'red';
		canvas.lineWidth = 2;
        canvas.moveTo(poses[0], poses[1]);
        canvas.lineTo(poses[2], poses[3]);
        canvas.moveTo(poses[0], poses[1]);
        canvas.lineTo(poses[4], poses[5]);
        canvas.moveTo(poses[2], poses[3]);
		canvas.lineTo(poses[6], poses[7]);
        canvas.moveTo(poses[4], poses[5]);
		canvas.lineTo(poses[8], poses[9]);
        canvas.moveTo(poses[10], poses[11]);
		canvas.lineTo(poses[12], poses[13]);
        canvas.moveTo(poses[12], poses[13]);
		canvas.lineTo(poses[16], poses[17]);
        canvas.moveTo(poses[16], poses[17]);
		canvas.lineTo(poses[20], poses[21]);
        canvas.moveTo(poses[10], poses[11]);
		canvas.lineTo(poses[14], poses[15]);
        canvas.moveTo(poses[14], poses[15]);
		canvas.lineTo(poses[18], poses[19]);
        canvas.moveTo(poses[10], poses[11]);
		canvas.lineTo(poses[22], poses[23]);
        canvas.moveTo(poses[12], poses[13]);
		canvas.lineTo(poses[24], poses[25]);
        canvas.moveTo(poses[22], poses[23]);
		canvas.lineTo(poses[24], poses[25]);
        canvas.moveTo(poses[22], poses[23]);
		canvas.lineTo(poses[26], poses[27]);
        canvas.moveTo(poses[26], poses[27]);
		canvas.lineTo(poses[30], poses[31]);
        canvas.moveTo(poses[24], poses[25]);
		canvas.lineTo(poses[28], poses[29]);
        canvas.moveTo(poses[28], poses[29]);
		canvas.lineTo(poses[32], poses[33]);
        canvas.stroke();
	}

	//사람으로 인식될 최소 점수
	var min_point_score = 0.4;

	//posenet 수평처리
	var flipHorizontal = false;

	//가이드,유저 이미지 변수
	const front_img = document.getElementById('front');
	const side_img = document.getElementById('side');
	const front_canvas = document.getElementById("front_canvas");
    const front_context = front_canvas.getContext("2d");
	const side_canvas = document.getElementById("side_canvas");
    const side_context = side_canvas.getContext("2d");

	var front_s = document.getElementById('front_s');
	const front_canvas_s = document.getElementById("front_canvas_s");
    const front_context_s = front_canvas_s.getContext("2d");

	var side_s = document.getElementById('side_s');
	const side_canvas_s = document.getElementById("side_canvas_s");
    const side_context_s = side_canvas_s.getContext("2d");

	//키포인트 좌표 담을 변수
	var front_pose;
	var side_pose;
	var front_data = [];
	var side_data = [];

	//두 이미지의 포즈인식 프로미스 처리 성공,실패용 변수 ( 프로미스 처리수, 사람인식 성공 수, check_on 메소드 실패과정 수 )
	var check_count = 0;
	var check_sucess = 0;
	var check_fail = 0;

	//디텍터 정의
	//const detectorConfig = {modelType: poseDetection.movenet.modelType.SINGLEPOSE_LIGHTNING};
	const detectorConfig = {
		modelType: poseDetection.movenet.modelType.SINGLEPOSE_THUNDER
		};

	//가이드 이미지 movenet 로드
	async function front_pose_load(){
		
		//거의 정사각형만 가능한 movenet 이미지 전처리
		front_canvas.width = <?=$max_width?>;
		front_canvas.height = <?=$max_width?>;

		// 캔버스에 업로드된 이미지를 그림
        front_context.drawImage(front_img, 0, 0,<?=$front_img_width?>,<?=$front_img_height?>);

		// canvas 에 있는 이미지를 img 태그로 넣기
        var front_dataurl = front_canvas.toDataURL("image/png");
        front_s.src  = front_dataurl;

		const front_detector = await poseDetection.createDetector(poseDetection.SupportedModels.MoveNet, detectorConfig);
		front_pose = await front_detector.estimatePoses(front_s);

		front_pose_on(front_pose);
	}

	//가이드 pose 처리
	async function front_pose_on(front_pose){
		
		for( var i = 0; i < front_pose[0].keypoints.length; i++){
			front_data[i*2] = front_pose[0].keypoints[i].x;
			front_data[i*2+1] = front_pose[0].keypoints[i].y;
		}

		if(front_pose[0].score > min_point_score){
			check_sucess++;
		}
		
		front_canvas_s.width = front_s.width;
		front_canvas_s.height = front_s.height;
		draw(front_data,front_context_s);

		check_count++;
		check_on();
	}

	//유저 이미지 movenet 로드
	async function side_pose_load(){
		
		//거의 정사각형만 가능한 movenet 이미지 전처리
		side_canvas.width = <?=$max_width?>;
		side_canvas.height = <?=$max_width?>;

		// 캔버스에 업로드된 이미지를 그림
        side_context.drawImage(side_img, 0, 0,<?=$side_img_width?>,<?=$side_img_height?>);

		// canvas 에 있는 이미지를 img 태그로 넣기
        var side_dataurl = side_canvas.toDataURL("image/png");
        side_s.src  = side_dataurl;
	
		const side_detector = await poseDetection.createDetector(poseDetection.SupportedModels.MoveNet, detectorConfig);
		side_pose = await side_detector.estimatePoses(side_s);

		side_pose_on(side_pose);
	}

	//유저 pose 처리
	async function side_pose_on(side_pose){
		for( var i = 0; i < side_pose[0].keypoints.length; i++){
			side_data[i*2] = side_pose[0].keypoints[i].x;
			side_data[i*2+1] = side_pose[0].keypoints[i].y;
		}

		if(side_pose[0].score > min_point_score){
			check_sucess++;
		}

		side_canvas_s.width = side_s.width;
		side_canvas_s.height = side_s.height;
		draw(side_data,side_context_s);

		//alert("점수를 계산합니다.");
		check_count++;
		check_on();
	}

	//두 프로미스 과정에 한쪽이상이 오류처리된 경우 호출
	function error(){
		
		check_fail++;
		alertback('사람이 인식되지 않습니다. 배경이 어두운지 너무 밝은지 확인해주세요.');
	}

	//두 프로미스 과정이 정상적으로 처리되었는지 확인 및 성공시 submit
	function check_on(){
		
		if(check_fail == 1){
			return;
		}

		if(check_count==2 && check_sucess==2){
			console.log(front_data);
			console.log(side_data);

		} else if (check_count==1 && check_sucess==0) {
			error();
		} else if (check_count==2 && check_sucess==0) {
			error();
		} else if (check_count==2 && check_sucess==1) {
			error();
		}
	}

</script>