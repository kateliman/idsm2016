<!DOCTYPE HTML>
<html>
    <head>
        <title>IDSM-Activity</title>
            <link href="https://fonts.googleapis.com/css?family=Abel|Architects+Daughter|Cookie|Fredericka+the+Great|Indie+Flower|Lobster|Pompiere|Sacramento|Sofia&amp;subset=latin-ext" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css?family=Cairo" 
					type="text/css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Vast+Shadow" 
					type="text/css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="css/StudentsActivityStyle.css">
            <link rel="stylesheet" type="text/css" href="css/Popup.css">
            <link rel="stylesheet" type="text/css" href="css/HTMLTags.css">
            <link rel="stylesheet" type="text/css" href="css/Buttons.css">
            <link rel="stylesheet" type="text/css" href="css/cssidsm.css">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>

    <body>
 <?php
        include("connection.php");
        try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //echo "<p class=\"success\">Connection réussie</p>";
                
                $sqlSelect = $conn->prepare("Select * from Subjects order by Title");
                $sqlSelect->execute();
                $allSubjects = $sqlSelect->fetchAll();
                
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    if(isset($_POST['subjectSelected']) & $_POST['subjectSelected'] != -1){
                        $subjectID = $_POST['subjectSelected'];
                    }
                    elseif(isset($_POST['currentSubjectID']) & $_POST['currentSubjectID'] != -1){
                        $subjectID = $_POST['currentSubjectID'];
                    }
                    else{
                        $subjectID = $allSubjects[0]->SubjectID;
                    }
                    $sqlSelect = $conn->prepare("Select * from Subjects where SubjectID = $subjectID");
                    $sqlSelect->execute();
                    $currentSubject = $sqlSelect->fetch(PDO::FETCH_OBJ);
                        
                        
                    if(isset($_POST['action']) & isset($_POST['skipValue'])){
                        switch($_POST['action']){
                            case 'prev':
                                if($_POST['skipValue'] != 0){
                                    $skipValue = $_POST['skipValue']-5;
                                }
                                else{
                                    $skipValue = 0;
                                }
                                break;
                            case 'next':
                                $skipValue = $_POST['skipValue']+5;
                                break;
                            default:
                                $skipValue = 0;
                                break;
                        }
                    }
                    else{
                        $skipValue = 0;
                    }
                }
                else{
                    $sqlSelect = $conn->prepare("Select * from Subjects limit 1");
                    $sqlSelect->execute();
                    $currentSubject = $sqlSelect->fetch(PDO::FETCH_OBJ);
                    $skipValue = 0;
                    }
                $sqlSelect = $conn->prepare("Select wd.WorkingDayID, wd.Date, wd.DayOfWeek, c.CourseID, p.Number 
                                            from WorkingDays as wd 
                                            join Courses as c on wd.WorkingDayID = c.WorkingDayID
                                            join Periods as p on c.PeriodID = p.PeriodID
                                            where c.SubjectID = $currentSubject->SubjectID
                                            order by wd.WorkingDayID, c.PeriodID
                                            limit $skipValue, 5");
                $sqlSelect->execute();    
                $workingDays = $sqlSelect->fetchAll();
                $countWorkingDays = count($workingDays);
            }
        catch(PDOException $e)
        {
            echo "<p class=\"error\">Connection échouée: " . $e->getMessage() . "</p>";
        }

    ?>
    <div class="top-panel">
        <span class="menu">
                				<a href='/SubjectActivity.php' target="_self" class="c1">Assistance</a>
                			</span>
                			<span class="menu">
                				<a href='/EditStudents.php' target="_self" class="c">Etudiants</a>
                			</span>
                			<span class="menu">
                				<a href='/EditSubjects.php' target="_self" class="c"><?php echo utf8_decode("Matières"); ?></a>
                			</span>
                			<span class="menu">	
                    			<a href='/index.php?week=this' target="_self" class="c">Accueil</a>
                    		</span>
<?php
    
    echo "<span class=\"first-level default-font-title\">".$currentSubject->Title."</span>
    
        <form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">
        <input type=\"hidden\" name=\"currentSubjectID\" value=\"$currentSubject->SubjectID\">
        <input type=\"hidden\" name=\"skipValue\" value=\"$skipValue\">
        <select id=\"subjects\" name=\"subjectSelected\" class=\"input-list\">
        <span class=\"controls\"><option selected value=\"-1\">".utf8_decode("Choisissez le matière...")."</option>";
        foreach($allSubjects as $subject){
            echo "<option value=\"{$subject['SubjectID']}\">{$subject['Title']}</option>";
        }
    echo "</select></span>
        <span class=\"controls\"><input type=\"image\" src=\"/images/arrow-left.png\" name=\"action\" value=\"prev\" class=\"arrow-icon\"></span>
        <span class=\"controls\"><input class=\"button save form-button-submit\" type=\"submit\" value=\"".utf8_decode("montrer activitée")."\" name=\"subjectSubmit\" ></span>
        <span class=\"controls\"><input type=\"image\" src=\"/images/arrow-right.png\" name=\"action\" value=\"next\" class=\"arrow-icon\"/></span>
        </form>";
?>
	
</div>
<div class="white-background">

<?php
        $sqlSelect = $conn->prepare("Select * from Students order by Surname");
        $sqlSelect->execute();    
        $students = $sqlSelect->fetchAll();
?>
<div class="stud-activity">
    <table >
        <th class="stud-number">##</th>
        <th><?php echo utf8_decode("étudiants"); ?></th>
<?php
        
        
        foreach($workingDays as $workingDay) {
            echo "<th>";
            echo $workingDay['Date']."<br>";
            echo $workingDay['Number']." - ".utf8_decode($workingDay['DayOfWeek'])."</th>";
        }
?>
        
<?php
        $i=1;
           foreach($students as $student) {
                echo "<tr>";
                echo "<td class=\"stud-number\">$i</td>";
                echo "<td class=\"stud-name\">".$student['Surname']." ".$student['Name']."</td>";
                foreach($workingDays as $workingDay) {
                    $sqlSelect = $conn->prepare("select IsPresent from Activity as ac
                                            where StudentID = {$student['StudentID']}
                                            and CourseID = {$workingDay['CourseID']}");
                    $sqlSelect->execute();    
                    $activity = $sqlSelect->fetch(PDO::FETCH_OBJ);
                    
                    if($activity != null & $activity->IsPresent=='1'){
                        echo "<td><input type=\"checkbox\" checked=\"checked\" disabled/></td>";
                    }
                    else{
                        echo "<td><input type=\"checkbox\" disabled/></td>";
                        }
                }
                
                
                // $sqlSelect = $conn->prepare("select ac.IsPresent from `Activity` as ac 
                //                              join `Courses` as c on ac.CourseID = c.CourseID
                //                              where StudentID = ".$student['StudentID'].
                //                              " and c.SubjectID = ".$currentSubject->SubjectID.
                //                              " limit $skipValue, 5");
                
                
                // if (count($activities)>0){
                // foreach($activities as $activitiy) {
                //     if($activitiy['IsPresent']=='1'){
                //         echo "<td><input type=\"checkbox\" checked=\"checked\" disabled/></td>";
                //     }
                //     else{
                //         echo "<td><input type=\"checkbox\" disabled/></td>";
                //     }
                // }}
                // else{
                //     $wokingDaysCount = count($workingDays);
                //     for($x=0; $x<$wokingDaysCount; $x++){
                //         echo "<td><input type=\"checkbox\" disabled/></td>";
                //     }
                // }
                echo "</tr>";
                $i+=1;
            }
?>
    </table>
       </div>

</div>

    </body>
</html>