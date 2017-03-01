<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <link href="css/cssidsm.css" rel="stylesheet" type="text/css">
        <title>IDSM - Entrer</title>
	</head>
	<body>
         <div class="header">
            <?php
            ob_start();
            session_name("idsm");
            session_start();
            //include("includes/header.php");
            ?>
        </div>
         <div class = "content">
         <?php
            include("connection.php");

            function startSessionForUser($userId, $login) {
               $_SESSION['valid'] = true;
               $_SESSION['timeout'] = time();
               $_SESSION['expire'] = $_SESSION['start'] + (1800);
               $_SESSION['userID'] = $userId;
               $_SESSION['login'] = $login;
            }

            function signIn($conn, $login, $pass) {
                
               $sqlSelect = $conn->prepare("SELECT * FROM Users
                     WHERE UserName='".$login."' AND password='".$pass."';");
               $sqlSelect->execute();
               $user = $sqlSelect->fetch(PDO::FETCH_OBJ);

               if ($user != null) {
                  startSessionForUser($user->UserID, $user->UserName);
                  header('Location: '.'index.php', true, $permanent ? 301 : 302);
               } else {
                   echo $user == null ? 'yes' : 'no';
                  header('Location: '.'Login.php?error=Les informations d\'identification de l\'utilisateur ne sont pas valides.', true, $permanent ? 301 : 302);
               }
               exit();
            }
            
            if (isset($_POST['signin'])) {
               try {
                    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                    signIn($conn, $_POST['login'], $_POST['pass']);
               } catch(PDOException $error) {
                  echo "<p>Error: ".$error->getMessage()."</p>\n";
               }
            }
            
            
         ?>
        <h1>Entrer</h1>
    
         <form action="Login.php" method="post">
               <p><input type="text" name="login" size="40" maxlength="40" placeholder="username" /></p>
               <p><input type="password" name="pass" size="40" maxlength="40" placeholder="Mot de passe" /></p>
               <p><input type="submit" name= "signin" value="Entrer" /></p>
               
         </form>
      </div>
        <div class = "footer">
            <?php
            //include ("includes/footer.php");
            ?>
        </div>
    </body>
</html>