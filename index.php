<?php
	$myfile = fopen("timer_data.txt", "r") or die("Unable to open file!");

	$firstLine = fgets($myfile);
	$firstLineTokens = explode(",", $firstLine);

	fclose($myfile);

	$expl = explode(":", $firstLineTokens[2]);
	$init = array();
	$init['h'] = intval($expl[0]);
	$init['i'] = intval($expl[1]);
	$init['s'] = intval($expl[2]);
	//print_r($init);
	
	function formatTime($input)
	{
		$ret = "";
		foreach($input as $i)
		{
			$i = strval($i);
			if(strlen($i) == 1)
			{
				$ret .= "0".$i;
			}
			else
			{
				$ret .= $i;
			}
			$ret .= ":";
		}

		return substr($ret,0,-1);
	}

	if($firstLineTokens[0]==1)
	{
		//running so calculate the time diff and subtract it from the initial dur
		$output = "";

		$now = date_create("now", new DateTimeZone("America/New_York"));
		$start = date_create($firstLineTokens[1], new DateTimeZone("America/New_York"));
		$diff = date_diff($now,$start);
		
		$delta = array();
		$delta['h'] = $init['h'] - $diff->format("%h");
		$delta['i'] = $init['i'] - $diff->format("%i");
		$delta['s'] = $init['s'] - $diff->format("%s");

		if($delta['s'] < 0)
		{
			$delta['i']--;
			$delta['s'] = 60 + $delta['s'];
		}
		if($delta['i'] < 0)
		{
			$delta['h']--;
			$delta['i'] = 60 + $delta['i'];
		}
		if($delta['h'] < 0)
		{
			$output = "00:00:00";

			//stop the timer
			$myfile = fopen("timer_data.txt", "w") or die("Unable to open file!");

			$txt = "0,".$firstLineTokens[1].",".$firstLineTokens[2];
			$txt .= "\n\nonly the first line is ever gotten here\nthe first value is either running or not running (0 or 1)\nthe second value is when the timer was started\nthe third value is the initial duration";

			fwrite($myfile, $txt);
			fclose($myfile);
		}
		else
		{
			$output = formatTime($delta);
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<script type="text/javascript">
		function refreshPage()
		{

			setTimeout(
				function(){
					location.reload();
				},
				5000
			);
		}
	</script>
</head>
<body onload="refreshPage()">
	<h1>Status: <?php echo ($firstLineTokens[0]==0)?("Paused"):("Running"); ?></h1>
    <h1>Time Remaining:</h1>
    <p><?php echo ($firstLineTokens[0]==0)?($firstLineTokens[2]):($output); ?></p>
</body>
</html>

