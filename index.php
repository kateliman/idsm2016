<!DOCTYPE html>
<html>
<head>
<title>IDSM 2016</title>
<meta http-equiv=content-type content="text/html; charset = "utf-8">

<link rel="stylesheet" type="text/css" href="css/cssidsm.css">
<link rel="stylesheet" type="text/css" href="css/Buttons.css">
		<link href="https://fonts.googleapis.com/css?family=Cairo" 
					type="text/css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Vast+Shadow" 
					type="text/css" rel="stylesheet">

</head>
<body class="body">
<?php
		ob_start();
        session_start();
        include("connection.php");
        
        try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $today = date("l");
                function getStartDay($today){
                	if ($today == "Monday"){
                		return "today";
                	}
                	else{
                		return "previous Monday";
                	}
                }
                function getEndDay($today){
                	switch($today){
                		case "Monday":
                			$endDay="next Saturday";
                			break;
                		case "Saturday":
                			$endDay="today";
                			break;
                		case "Sunday":
                			$endDay="previous Saturday";
                			break;
                		default:
                			$endDay="next Saturday";
                			break;
                	}
                	return $endDay;
                }
               
                // default current week parameters
                if(isset($_GET['week']) & isset($_GET['action'])){
                	switch($_GET['action']){
                		case 'prev':
                			$weekNumber = $_GET['week'] -1 ;
                			$start=strtotime("$weekNumber Week ".getStartDay($today));
							$end=strtotime("$weekNumber week ".getEndDay($today));
                			break;
                		case 'this':
                			$weekNumber = 0;
                			$start=strtotime(getStartDay($today));
							$end=strtotime(getEndDay($today));
                			break;
                		case 'next':
                			$weekNumber = $_GET['week'] +1;
                			$start=strtotime("$weekNumber Week ".getStartDay($today));
							$end=strtotime("$weekNumber week ".getEndDay($today));
                			break;
                		default:
                			break;
             
                	}
                }
                else{
                	$weekNumber = 0;
                	$start=strtotime(getStartDay($today));
					$end=strtotime(getEndDay($today));
                }
                
				while ($start <= $end) {
					$currentWeek[] = $start;
					$start = strtotime("+1 day", $start);
				}
				
				// select all working days
				$dates = "('";
                    foreach($currentWeek as $date){
                        $dates = $dates.date("Y-m-d", $date)."','";
                    }
                    $dates = trim($dates, ",'");
                    $dates = $dates."')";
                    
                $sqlSelect = $conn->prepare("Select * from WorkingDays WHERE Date in ".$dates." order by Date");    
                $sqlSelect->execute();    
                $workingDays = $sqlSelect->fetchAll();
                
                // select all periods
                $sqlSelect = $conn->prepare("Select * from Periods order by Number");    
                $sqlSelect->execute();    
                $periods = $sqlSelect->fetchAll();
                
                
                
                
                
            }
        catch(PDOException $e)
        {
            echo "<p class=\"error\">Connection échouée: " . $e->getMessage() . "</p>";
        }

    ?>
    

		<div>
			<span class="menu">
				<a href='/SubjectActivity.php' target="_self" class="c1">Assistance</a>
			</span>
			<span class="menu">
				<a href='/EditStudents.php' target="_self" class="c1">Etudiants</a>
			</span>
			<span class="menu">
				<a href='/EditSubjects.php' target="_self" class="c1"><?php echo utf8_decode("Matières"); ?></a>
			</span>
			<span class="menu">	
    			<a href='/index.php?week=this#' target="_self" class="c">Accueil</a>
    		</span>
			<span class="header" id="pagename">l'Horaire de M1 IDSM</span>
		</div>
		<div class="mainbody"><!--Calendar-->
		<table cellpadding="2" width="100%" >
		<tr class="tableheader" >
<?php
	echo "<th>";
	echo "<span id=\"dayName\">Period</span><br>";
	echo "</th>";
	foreach($workingDays as $day){
		echo "<th>";
			echo "<span id=\"dayName\" width=\"15%\" >{$day['DayOfWeek']}</span><br>";
			echo "<span id=\"date\">{$day['Date']}</span>";
		echo "</th>";
	}
	
	foreach($periods as $period){
		echo "<tr >";
		echo "<td id=\"period\"class=\"tabled\" >";
		echo "<span id=\"1ParaName\">{$period['Number']}</span><br>";
		echo "<span id=\"1ParaName\">{$period['StartTime']}</span><br>";
		echo "<span id=\"1ParaName\">{$period['EndTime']}</span><br>";
		echo "</td>";
		foreach($workingDays as $day){
			$sqlSelect = $conn->prepare("SELECT * 
										FROM  `Courses` as c
										JOIN  `Subjects` as s ON c.SubjectID = s.SubjectID
										JOIN  `Profs` as p ON p.ProfID = c.ProfID
										WHERE c.WorkingDayID = {$day['WorkingDayID']}
										AND c.PeriodID = {$period['PeriodID']}");    
            $sqlSelect->execute();
            
            $course = $sqlSelect->fetch(PDO::FETCH_OBJ);
            echo "<td class=\"tabled\">";
			echo "<span id=\"PeriodName\" id=\"1ParaName\">$course->Title</span><br>";
			echo "<span id=\"prof\">$course->ProfName</span><br>";
			if($course != null){
				echo "<span id=\"prof\"><a id=\"activite\" href=\"/CourseActivity.php?course=$course->CourseID\">".utf8_decode("Activité")."</a></span>";
			}
			echo "</td>";
        }
		echo "</tr>";
	}
?>


	</table>
</div>

	<div class="footer"><!--Footer-->
	<?php
		echo "<a href=\"?week=$weekNumber&action=prev\" class=\"c\">Semaine precedente</a>";
		echo "<a href=\"?week=$weekNumber&action=this\" class=\"c\">Semaine en cours</a>";
		echo "<a href=\"?week=$weekNumber&action=next\" class=\"c\">Semaine prochaine</a>";
	?>
	</div>

</body>
</html>
