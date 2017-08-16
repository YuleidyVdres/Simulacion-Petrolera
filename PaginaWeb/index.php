
<?php
    //session_start();
?>    
<html>

<head >
<meta charset="UTF-8">
        <title> INICIO </title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="barril22">
<img id="aceite"; src="aceite.png"; width="100%" />
</div>
<div class="dispositivo">
<img id="disp"; src="dispositivos.png"; width="60%" />
</div>
<div class="fondo22">
<img id="fondo"; src="fondoform.png";/>
</div>
<div class="form33">
        <form name="form1" action="" method="post" class="formulario2">
            <legend class="Titulo">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Dirección IP</legend>
            <br/>
            <fieldset>
                <br/>
                <input type="text" name="direcip" required/>
                <br/><br/>
                <input type="submit" class="button"name="boton" value="Enviar"/>
               
            </fieldset>               
                
        </form>
    </div>
     <?php
        
        /*if(isset($_POST["boton"])){
            header('Location: petroleo.php');
        }*/

        if(isset($_POST["direcip"])){
		    $_SESSION["direcip"] = $_POST["direcip"];
		    //echo "La ip es".$_SESSION["direcip"];
            header('Location: petroleo.php');
	    }

        if(isset($_SESSION["direcip"]))
            header('Location: petroleo.php');

               
    ?>

</body>
</html>