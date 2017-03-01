<!DOCTYPE HTML>
<html>
    <head>
        <title>IDSM-Edit</title>
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
        
    </head>

    <body>
    <?php
        include("connection.php");
        ob_start();
		session_name("idsm");
        session_start();
        function prepareValue($value){
            $value = trim($value);
            $value = utf8_decode($value);
            return $value;
        }
        try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn->exec("set names UTF8");
                $conn->exec("set character set UTF8");
                //echo "<p class=\"success\">Connection réussie</p>";
                if($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['action'])){
                    switch ($_POST['action']) {
                            case 'remove':
                                $sqlDelete = $conn->prepare("DELETE FROM Students WHERE StudentID = {$_POST['studentID']} ");
                                $sqlDelete->execute();
                                break;
                            case 'submitEdit':
                                echo utf8_encode($_POST['Surname']);
                                $sqlUpdate = $conn->prepare("UPDATE Students SET Surname = \"".prepareValue($_POST['surname'])."\", Name = \"".prepareValue($_POST['name'])."\"  WHERE StudentID = {$_POST['studentID']} ");
                                $sqlUpdate->execute();
                                break;
                            case 'submitAdd':
                                $sqlInsert = $conn->prepare("INSERT INTO Students (Surname, Name) VALUES (\"".prepareValue($_POST['surname'])."\",\"".prepareValue($_POST['name'])."\")");
                                $sqlInsert->execute();
                                break;
                            default:
                                // code...
                                break;
                        }
                }
                
            }
        catch(PDOException $e)
        {
            echo "<p class=\"error\">Connection échouée: " . $e->getMessage() . "</p>";
        }

    ?>
        <!--<div class="white-background">-->
            <div class="top-panel">
                <span class="first-level"><?php echo utf8_decode("Etudiants");?>
                            <span class="menu">
                				<a href='/SubjectActivity.php' target="_self" class="c">Assistance</a>
                			</span>
                			<span class="menu">
                				<a href='/EditStudents.php' target="_self" class="c1">Etudiants</a>
                			</span>
                			<span class="menu">
                				<a href='/EditSubjects.php' target="_self" class="c"><?php echo utf8_decode("Matières"); ?></a>
                			</span>
                			<span class="menu">	
                    			<a href='/index.php?week=this' target="_self" class="c">Accueil</a>
                    		</span>
                </span>
            </div>
            <div class="list">
                <?php
                $sqlSelect = $conn->prepare("Select * from Students order by Surname");    
                $sqlSelect->execute();    
                $students = $sqlSelect->fetchAll();
                ?>
                <table>
                    <th>nom</th>
                    <th><?php echo utf8_decode("prénom"); ?></th>
                    <th>Actions</th>
                    <?php
                    foreach($students as $student) {
                        echo "<tr>";
                        echo "<form name=\"studentForm\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"studentID\" value=\" {$student['StudentID']} \"/>";
                        echo "<input type=\"hidden\" name=\"surname\" value=\" {$student['Surname']} \"/>";
                        echo "<input type=\"hidden\" name=\"name\" value=\" {$student['Name']} \"/>";
                        echo "<td>".$student['Surname']."</td>";
                        echo "<td>".$student['Name']."</td>";
                        echo "<td>  
                            <input formaction=\"#popup-form\" type=\"image\" src=\"/images/Edit-icon.png\" name=\"action\" value=\"edit\" class=\"action-icon\">
                            <input type=\"image\" src=\"/images/remove-icon.png\" name=\"action\" value=\"remove\" class=\"action-icon\"/>
                            </td>";
                        echo "</form>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <a class="button add-new" href="?action=add#popup-form">ajouter</a>
           <!-- </div>
        </div>-->
        
        
        <div id="popup-form" class="overlay">
            
            <div class="popup">
                <div class="top-panel">
                    <?php
                    if($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['action'])){
                        if($_POST['action']=="edit"){
                            $formTitle = utf8_decode("Editer étudiant");
                        }
                    }
                    elseif ($_SERVER["REQUEST_METHOD"] == "GET" & isset($_GET['action'])) {
                        if($_GET['action']=="add"){
                            $formTitle = utf8_decode("Ajouter étudiant");
                        }
                    }
                    echo "<span class=\"first-level\">$formTitle</span>";
                    ?>
                </div>
                    <?php 
                    if (isset($_SESSION['userID'])){
                        
                    
                echo "<form class=\"content\" name=\"form-edit\" method=\"post\" action=\"{$_SERVER["PHP_SELF"]}\">";
                
                
                if($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['action'])){
                    switch ($_POST['action']) {
                            case 'edit':
                                $action = "submitEdit";
                                $surname = utf8_decode($_POST['surname']);
                                $name = utf8_decode($_POST['name']);
                                echo "<input type=\"hidden\" name=\"studentID\" value=\"{$_POST['studentID']}\"/>";
                                echo "<label for=\"name\" class=\"second-level\">".utf8_decode("prénom")."</label>";
                                echo "<input type=\"text\" name=\"name\" class=\"input-text\" value=\"$name\">";
                                echo "<label for=\"surname\" class=\"second-level\">nom</label>";
                                echo "<input type=\"text\" name=\"surname\" class=\"input-text\" value=\"$surname\">";
                                break;
                            case 'add':
                                
                                break;
                            default:
                                // code...
                                break;
                        }
                }
                elseif ($_SERVER["REQUEST_METHOD"] == "GET" & isset($_GET['action'])) {
                    $action = "submitAdd";
                    echo "<label for=\"name\" class=\"second-level\">".utf8_decode("prénom")."</label>";
                    echo "<input type=\"text\" name=\"name\" class=\"input-text\">";
                    echo "<label for=\"surname\" class=\"second-level\">nom</label>";
                    echo "<input type=\"text\" name=\"surname\" class=\"input-text\">";
                    
                }
                echo "<input type=\"hidden\" name=\"action\" value=\"$action\"/>";
                echo "<span><input type=\"submit\" class=\"popup-button form-button-submit\" value=\"sauvegarder\"></span>";
                echo "<span><input type=\"reset\" class=\"popup-button form-button-reset\" value=\"".utf8_decode("réinitialiser")."\"></span>";
                echo "<span><a class=\"popup-button form-button-cancel\" href=\"". strtok($_SERVER["REQUEST_URI"],'?')."\">annuler</a></span>";
                echo "</form>";
                    }
                else{
                    echo "<p class=\"error\">".utf8_decode('Vous n\'avez pas de permission! Signez-vous, s\'il vous plaît')." <a href=\"/Login.php\">ici</a> . </p>";
                    echo "<span><a class=\"popup-button form-button-cancel\" href=\"". strtok($_SERVER["REQUEST_URI"],'?')."\">fermer</a></span>";
                }
                ?>
                
            </div>    
            </div>
        </div>
            
        <!--</div>-->
    </body>
</html>