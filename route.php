<?php
session_start();
?>
<!DOCTYPE HTML>
<html lang="zh"><head>
<meta charset="UTF-8">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1,user-scalable=no">
<link href="resource/icon-57x57-precomposed.png" rel="apple-touch-icon-precomposed">
<link href="resource/icon-114x114-precomposed.png" sizes="114x114" rel="apple-touch-icon-precomposed">
<title>WOL Power by biner</title>
<style type="text/css">
body{
	background-color: #eee;
}
.main{
	text-align:center;
	margin-top: 20px;
}
.input{
	
}
.input input{
	height: 30px;
	line-height: 30px;
	padding-left: 10px;
	width:90%;
	font-size: 20px
}
.btn{
	margin-top: 10px;
}
.btn input{
	height: 40px;
	line-height: 40px;
	width:95%;
	-webkit-appearance:none ;
}
.info{
	border: 2px #808080 solid;
	height: 40px;
	line-height: 40px;
	font-size: 20px;
}
.success{
	color: green;
}
.error{
	color: red;
}
.copyright{
	color: #808080;
	bottom:10px;
	position:fixed;
	width:90%;
}
.copyright a{
	color: #808080;
}
</style>
</head>
<?php
if (isset($_GET['mac'])) {
	include('library/Openwrt.class.php');
	$config = include('config.php');
	$Openwrt = new Openwrt();
	//$Openwrt->clear();
	$Openwrt->login($config);
	$result = $Openwrt->setWol($_GET['mac']);
}
?>
<body>
<div class="main">
<?php if(isset($_GET['mac'])){ if ($result==true) {?>
	<div class="info success">Remote Wake Success</div>
<?php }else{?>
	<div class="info error">Remote Wake Error</div>
<?php }}else{?>
	<form action="?" method="get">
		<div class="input">
			<input type="text" name="mac" value="" placeholder="MAC ADDRESS">
		</div>
		<div class="btn">
			<input type="submit" value="submit">
		</div>
	</form>
<?php }?>
<div class="copyright"><a href="https://www.biner.me">www.biner.me</a></div>
</div>
</body>
</html>
