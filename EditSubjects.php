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
        function prepareValue($value){
            $value = trim($value);
            $value = utf8_decode($value);
            return $value;
        }
        try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['action'])){
                    switch ($_POST['action']) {
                            case 'remove':
                                $sqlDelete = $conn->prepare("DELETE FROM Subjects WHERE SubjectID = {$_POST['subjectID']} ");
                                $sqlDelete->execute();
                                break;
                            case 'submitEdit':
                                echo utf8_encode($_POST['subjectTitle']);
                                $sqlUpdate = $conn->prepare("UPDATE Subjects SET Title = \"".prepareValue($_POST['subjectTitle'])."\" WHERE SubjectID = {$_POST['subjectID']} ");
                                $sqlUpdate->execute();
                                break;
                            case 'submitAdd':
                                $sqlInsert = $conn->prepare("INSERT INTO Subjects (Title) VALUES (\"".prepareValue($_POST['subjectTitle']."\")"));
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
       
        <div class="white-background">
            <div class="top-panel">
                <span class="first-level"><?php echo utf8_decode("Matières"); ?>
                <span class="menu">
                				<a href='/SubjectActivity.php' target="_self" class="c">Assistance</a>
                			</span>
                			<span class="menu">
                				<a href='/EditStudents.php' target="_self" class="c">Etudiants</a>
                			</span>
                			<span class="menu">
                				<a href='/EditSubjects.php' target="_self" class="c1"><?php echo utf8_decode("Matières"); ?></a>
                			</span>
                			<span class="menu">	
                    			<a href='/index.php?week=this' target="_self" class="c" style="background-color:white">Accueil</a>
                    		</span>
                </span>
            </div>
            <div class="list">
                <?php
                $sqlSelect = $conn->prepare("Select * from Subjects order by Title");    
                $sqlSelect->execute();    
                $subjects = $sqlSelect->fetchAll();
                
                ?>
                <table>
                    <th>Titre</th>
                    <th>Actions</th>
                    <?php
                    foreach($subjects as $subject) {
                        echo "<tr>";
                        echo "<form name=\"subjectForm\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"subjectID\" value=\"{$subject['SubjectID']}\"/>";
                        echo "<input type=\"hidden\" name=\"subjectTitle\" value=\"".utf8_encode($subject['Title'])."\"/>"; //??????
                        echo "<td>".$subject['Title']."</td>";
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
                
            </div>
        </div>
        
        <div id="popup-form" class="overlay">
            
            <div class="popup">
                <div class="top-panel"> 
                    <?php
                    if($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['action'])){
                        if($_POST['action']=="edit"){
                            $formTitle = utf8_decode("Editer matière");
                        }
                    }
                    elseif ($_SERVER["REQUEST_METHOD"] == "GET" & isset($_GET['action'])) {
                        if($_GET['action']=="add"){
                            $formTitle = utf8_decode("Ajouter matière");
                        }
                    }
                    echo "<span class=\"first-level\">$formTitle</span>";
                    ?>
                </div>
                
                <?php
                echo "<form class=\"content\" name=\"form-edit\" method=\"post\" action=\"{$_SERVER["PHP_SELF"]}\">";
                echo "<label for=\"title\" class=\"second-level\">title</label>";
                if($_SERVER["REQUEST_METHOD"] == "POST" & isset($_POST['action'])){
                    switch ($_POST['action']) {
                            case 'edit':
                                $action = "submitEdit";
                                $title = utf8_decode($_POST['subjectTitle']);
                                echo "<input type=\"hidden\" name=\"subjectID\" value=\"{$_POST['subjectID']}\"/>";
                                echo "<input type=\"text\" name=\"subjectTitle\" class=\"default-font input-text\" value=\"$title\">";
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
                    echo "<input type=\"text\" name=\"subjectTitle\" class=\"default-font input-text\">";
                }
                echo "<input type=\"hidden\" name=\"action\" value=\"$action\"/>";
                echo "<span><input type=\"submit\" class=\"popup-button form-button-submit\" value=\"sauvegarder\"></span>";
                echo "<span><input type=\"reset\" class=\"popup-button form-button-reset\" value=\"".utf8_decode("réinitialiser")."\"></span>";
                echo "<span><a class=\"popup-button form-button-cancel\" href=\"". strtok($_SERVER["REQUEST_URI"],'?')."\">annuler</a></span>";
                echo "</form>";
                ?>
            </div>
            
        </div>
    </body>
</html>