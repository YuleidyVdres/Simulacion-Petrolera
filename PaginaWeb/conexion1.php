<?php
require_once dirname(__FILE__) . '/ModbusMaster.php';
// Create Modbus object
$ip = "192.168.250.3";//Dirección IP que tiene asignado el nodeMCU
$modbus = new ModbusMaster($ip, "TCP"); // se crea el objeto Modbus
$data_true = array(TRUE); 
$data_false = array(FALSE);


    $led = $_POST["led"]; // los valores que recibe por ajax desde la pagina petroleo.php
    $value =$_POST["value"]; // los valores que recibe por ajax desde la pagina petroleo.php
    $array = array( // se declara un array con las variables a devolver a la pagina petroleo.php
      "led" => $led,
      "value" => $value,
      "value_0"=>0,
      "value_1"=>0,
      "value_2"=>0,
      "value_6"=>0,
      "value_7"=>0,
      "value_8"=>0
    );
    try {


      if($array["led"]==3 || $array["led"]==4){//Envia los cambios realizados desde la página web con respeto a los botones P y T
        if($array["value"] == "false"){//Apagar
          $modbus->writeSingleCoil(0, $array["led"], $data_false);
            $array2 = array(
            "led" => $led,
            "value" => "true",
            );
            echo json_encode($array2);//Devuelve los valores modificados a  petroleo.php
            exit();
        }
        else{//Encender
          if($array["value"] == "true"){
            $modbus->writeSingleCoil(0, $array["led"], $data_true);
            $array2 = array(
            "led" => $led,
            "value" => "false",
            );
            echo json_encode($array2);//Devuelve los valores modificados a  petroleo.php
            exit();
          }
        }
      }// FIN IF DIGITAL
      else{
        if($array["led"]==10 || $array["led"]==11){ // Entra cuando se envía una solicitud de un cambio analogico al nodemcu, salida analogica


          $modbus->writeSingleRegister(0,$led,array($value),'INT'); // función que me permite enviar peticiones de salidas analogicas al nodemcu


          $array = array(
            "led" => $led,
            "value" => $value,
            "value_0"=>0,
            "value_1"=>0,
            "value_2"=>0,
            "value_6"=>0,
            "value_7"=>0,
            "value_8"=>0
          );


          echo json_encode($array);
          exit();
        }else{
          //-----------------lectura digitales y analogicas


            $coils = $modbus->readCoils(0, 0, 3);//Lee 3 coils desde 0 hasta 2
            $array["value_0"] = $coils[0] /*==1?"true":"false"*/;
            $array["value_1"] = $coils[1]/*==1?"true":"false"*/;
            $array["value_2"] = $coils[2]/*==1?"true":"false"*/;


            $recData=$modbus->readMultipleRegisters(0,6,3); //Lee 3 registros del 6 al 8 (entrada analogica)
            $array["value_6"] = array($recData[0],$recData[1]);
            $array["value_7"] = array($recData[2],$recData[3]);
            $array["value_8"] = array($recData[4],$recData[5]);
            
            echo json_encode($array);//Devuelve los valores modificados a petroleo.php
            exit();
        }
      }
      
    }// fin try
    catch (Exception $e) { 
        echo $e;
        conectar($ip);
        exit;
    }
function conectar($dirip){
  $_SESSION["modbus"] = new ModbusMaster($dirip, "TCP");
}
?>
