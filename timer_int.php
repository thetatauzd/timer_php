<?php
	$actionMessage = "";

	$myfile = fopen("timer_data.txt", "r") or die("Unable to open file!");

	$firstLine = fgets($myfile);
	$firstLineTokens = explode(",", $firstLine);
	//get the string value for display purposes of the start duration
	$temp = "";
	$expl = explode(":", $firstLineTokens[2]);
	foreach($expl as $e)
	{
		$temp .= $e.":";
	}
	$firstLineTokens[2] = substr($temp,0,-1);

	fclose($myfile);

	if(isset($_POST['submitted']) && $_POST['submitted']!="")
	{
		$txt = "";

		if(isset($_POST['setUp']) && $_POST['setUp']!="")
		{
			$dur = trim($_POST['dur']);
			if($dur == "")
			{
				$actionMessage .= "Duration cannot be left empty.<br>";
			}
			else
			{
				$splitDur = explode(":", $dur);
				if(count($splitDur)!=3)
				{
					$actionMessage .= "Please format the duration as hh:mm:ss.<br>";
				}
				else
				{
					if($splitDur[0] > 99 || $splitDur[0] < 0)
					{
						$actionMessage .= "Hours out of range.<br>";
					}
					if($splitDur[1] > 59 || $splitDur[1] < 0)
					{
						$actionMessage .= "Minutes out of range.<br>";
					}
					if($splitDur[2] > 59 || $splitDur[2] < 0)
					{
						$actionMessage .= "Seconds out of range.<br>";
					}
				}
			}


			if($actionMessage == "")
			{
				//edit the file to have the new dur
				$myfile = fopen("timer_data.txt", "w") or die("Unable to open file!");

				$txt = "0,YYYY-MM-DD HH-MM-SS,".$dur;
				$txt .= "\n\nonly the first line is ever gotten here\nthe first value is either running or not running (0 or 1)\nthe second value is when the timer was started\nthe third value is the initial duration";

				if(fwrite($myfile, $txt))
				{
					$firstLineTokens[2] = $splitDur[0] . ":" . $splitDur[1] . ":" . $splitDur[2];
					$actionMessage .= "Timer Updated!<br>";
				}
				else
				{
					$actionMessage .= "Could not set up timer at the moment<br>";
				}

				fclose($myfile);
			}
		}
		else if(isset($_POST['start']) && $_POST['start']!="")
		{
			//edit the file to have the proper start date
			$myfile = fopen("timer_data.txt", "w") or die("Unable to open file!");

			$now = date_create("now",new DateTimeZone("America/New_York"));

			$startDate = date_format($now,"Y-m-d H:i:s");

			$txt = "1,".$startDate.",".$firstLineTokens[2];
			$txt .= "\n\nonly the first line is ever gotten here\nthe first value is either running or not running (0 or 1)\nthe second value is when the timer was started\nthe third value is the initial duration";

			if(fwrite($myfile, $txt))
			{
				$firstLineTokens[0] = 1;
				$actionMessage .= "Timer Updated!<br>";
			}
			else
			{
				$actionMessage .= "Could not set up timer at the moment<br>";
			}

			fclose($myfile);
		}
		else if(isset($_POST['stop']) && $_POST['stop']!="")
		{
			//edit the file to have the proper start date
			$myfile = fopen("timer_data.txt", "w") or die("Unable to open file!");

			$txt = "0,".$firstLineTokens[1].",".$firstLineTokens[2];
			$txt .= "\n\nonly the first line is ever gotten here\nthe first value is either running or not running (0 or 1)\nthe second value is when the timer was started\nthe third value is the initial duration";

			if(fwrite($myfile, $txt))
			{
				$firstLineTokens[0] = 0;
				$actionMessage .= "Timer Updated!<br>";
			}
			else
			{
				$actionMessage .= "Could not set up timer at the moment<br>";
			}

			fclose($myfile);
		}
		else if(isset($_POST['resume']) && $_POST['resume']!="")
		{
			//edit the file to have the proper start date
			$myfile = fopen("timer_data.txt", "w") or die("Unable to open file!");

			//$startDate = date("Y-m-d h:i:s");

			$txt = "1,".$firstLineTokens[1].",".$firstLineTokens[2];
			$txt .= "\n\nonly the first line is ever gotten here\nthe first value is either running or not running (0 or 1)\nthe second value is when the timer was started\nthe third value is the initial duration";

			if(fwrite($myfile, $txt))
			{
				$firstLineTokens[0] = 1;
				$actionMessage .= "Timer Updated!<br>";
			}
			else
			{
				$actionMessage .= "Could not set up timer at the moment<br>";
			}

			fclose($myfile);
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<script type="text/javascript">
<?php
	if($firstLineTokens[0] == 1)
	{
?>
		
<?php
	}
?>
	</script>
</head>
<body>
	<p><?php echo $actionMessage; ?></p>

	<h1>Current Status: <?php echo ($firstLineTokens[0]==0)?("Paused"):("Running"); ?></h1>
	<hr>
    <h3>Timer Set Up:</h3>
	<form id="main" name="main" method="post">
		<input type="hidden" name="submitted" id="submitted" value="submitted">
		<p>Duration(hh:mm:ss): <input type="text" name="dur" value="<?php echo strval($firstLineTokens[2]); ?>"></p>
		<p><input type="submit" name="setUp" value="Set Up"></p>
		<hr>
		<h3>Start/Stop</h3>
		<p><input type="submit" name="start" value="Start"> <input type="submit" name="stop" value="Stop"> <input type="submit" name="resume" value="resume"></p>
	</form>	
</body>
</html>

