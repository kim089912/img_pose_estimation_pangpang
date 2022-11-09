<? include "../../inc/head.php"; ?>
<form action="./filming_processing_movenet.php" method="post" enctype="multipart/form-data">
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<span> 정면 이미지 </span>
    <input type="file" name="front_img">
	<span> 측면 이미지 </span>
    <input type="file" name="side_img">
    <input type="submit" value="check" name="submit">
</form>