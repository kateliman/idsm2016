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
    </head>

    <body>
    <?php
        include("connection.php");
        
        if(isset($_GET['course'])){
             $courseID = $_GET['course'];
        }
       
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "<p class=\"success\">Connection réussie</p>";
    
            if ($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['absent'])){
                    
                $absent = $_POST['absent'];
                    
                if(isset($_POST['present'])){
                    $presentStudents = "(";
                    $absent = array_diff($_POST['absent'], $_POST['present']);
                    foreach($_POST['present'] as $id){
                        $sqlSelect = $conn->prepare("select ActivityID from Activity WHERE CourseID = $courseID AND StudentID = $id");
                        $sqlSelect->execute();
                        $actID = $sqlSelect->fetch(PDO::FETCH_OBJ);
                        if($actID != null){
                            $sqlQuery = $conn->prepare("Update Activity SET IsPresent=1 WHERE ActivityID=$actID->ActivityID");
                        }
                        else{
                            $sqlQuery = $conn->prepare("INSERT INTO Activity (IsPresent, StudentID, CourseID)
                                                        VALUES (1, $id, $courseID)");
                        }
                        $sqlQuery->execute();
                    }
                }
                if(count($absent) > 0){
                    foreach($absent as $id){
                        $sqlSelect = $conn->prepare("select ActivityID from Activity WHERE CourseID = $courseID AND StudentID = $id");
                        $sqlSelect->execute();
                        $actID = $sqlSelect->fetch(PDO::FETCH_OBJ);
                        if($actID != null){
                            $sqlQuery = $conn->prepare("Update Activity SET IsPresent=0 WHERE ActivityID=$actID->ActivityID");
                        }
                        else{
                            $sqlQuery = $conn->prepare("INSERT INTO Activity (IsPresent, StudentID, CourseID)
                                                        VALUES (0, $id, $courseID)");
                        }
                        $sqlQuery->execute();
                    }
                }
            }
        }
        catch(PDOException $e)
        {
            echo "<p class=\"error\">Connection échouée: " . $e->getMessage() . "</p>";
        }
    ?> 
        
        <div class="white-background">
            <span class="menu">
                				<a href='/SubjectActivity.php' target="_self" class="c">Assistance</a>
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
            <form method="POST" action="">    
                <div class="top-panel">
                    <?php
                        $sqlSelect = $conn->prepare("Select s.* from Subjects as s join Courses as c on s.SubjectID = c.SubjectID where CourseID = $courseID");
                        $sqlSelect->execute();    
                        $subject = $sqlSelect->fetch(PDO::FETCH_OBJ);
                        echo "<span class=\"first-level\">".$subject->Title."</span>";
                    ?>
                    <span><input class="button save form-button-submit" type="submit" value="sauvegarder"/></span>
                            
                
                        
                </div>
                        
                <?php
                    $sqlSelect = $conn->prepare("Select s.*, a.ActivityID, a.IsPresent from Students as s left join Activity as a
                                                on s.StudentID = a.StudentID 
                                                and a.CourseID =".$courseID." order by Surname");
                    $sqlSelect->execute();    
                    $activities = $sqlSelect->fetchAll();
                ?>
                    <table class="stud-activity">
                        <th class="stud-number">##</th>
                        <th>etudiants</th>
                        <th>presense</th>
                        
                    <?php
                        $i=1;
                        foreach($activities as $activitiy) {
                            echo "<input type=\"hidden\" name=\"absent[]\" value=\" {$activitiy['StudentID']} \"/>";
                            echo "<tr>";
                            echo "<input type=\"hidden\" value=\" {$activitiy['ActivityID']} \"/>";
                            echo "<td>$i</td>";
                            echo "<td class=\"stud-name\"> {$activitiy['Surname']} {$activitiy['Name']} </td>";
                            if($activitiy['IsPresent'] != null & $activitiy['IsPresent'] == '1'){
                                echo "<td><input name=\"present[]\" type=\"checkbox\" checked=\"checked\" value=\" {$activitiy['StudentID']} \"/></td>";
                            }
                            else{
                                echo "<td><input name=\"present[]\" type=\"checkbox\" value=\" {$activitiy['StudentID']} \"/></td>";
                            }
                            echo "</tr>";
                            $i+=1;
                        }
                    ?>
                    </table>
            </form>
        </div>
    </body>
</html>