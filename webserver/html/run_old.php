<?php 		
	if(!isset($_GET["dataset"])){
		echo "Illegal request";
		exit();
	}
	$dataset = $_GET["dataset"];
	ob_implicit_flush(1);
	ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Default functionality</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<style> 
		/* Optional: Makes the sample page fill the window. */
		body {
			padding: 30px;
		}
		
		#console{
			margin:20px;
			padding: 10px;
			width:80%;
			height:400px;
			overflow:auto;
			font-size:small;
			background-color:lightgrey;
			border: 1px solid rgba(99, 93, 93, 0.29);
		}
	</style>
</head>
<body>
	<h3 id="heading">Please wait while the task is completed</h3>
	<div id="progressbar" ></div>
	<a href="mapview.php?dataset=<?php echo $dataset ?>">Check the map</a>
	<a href="outbaselink/<?php echo $dataset ?>">Check the output folder</a>
	<div id="console" contenteditable="true">
		<b>Python output</b><br>
	</div>
	<script>
		$( "#progressbar" ).progressbar({
		  value: 0
		});
	</script>
</body>
</html>

<?php 
	//Flush and send everything in the output buffer to client
	flush();
	ob_flush();
	//Creating a subprocess with its STDOUT as $handle
	//$handle behaves like a file pointer
	$handle = popen("python modulelink/MinHash/matcher.py $dataset", 'r');
	//Loop until end of file
	while (!feof($handle)) {
		//Reach a minimum buffer size before flushing
		echo str_repeat(' ',4096);
		$output = fgets($handle);
		//echo ".".$data.".<br>";
		//$progress = intval(fgets($handle));
		//$infotext = fgets($handle);
		//$progress = 0;
		if(strncmp($output,"prog",4)==0){
			$progress = intval(substr($output,5));
			echo '<script>$("#progressbar").progressbar( "value", '.$progress.' );</script>';
		} else if(strncmp($output,"succ",4)==0){
			echo '<script>document.getElementById("heading").innerHTML="Process completed successfully";</script>';			
		} else  {
			$output = str_replace("\n","<br>",$output);
			echo '<script>var elem = document.getElementById("console");'
				.'elem.innerHTML+="'.$output.'";'
				.'elem.scrollTop = elem.scrollHeight;'
				.'</script>';			
		}
	
		
		flush();
		ob_flush();
	}
	
	//close the process
	pclose($handle);

?>



