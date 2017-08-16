#define cantidad_sensores 6
#define tiempo_muestreo 1000

const int pines_analogicos[cantidad_sensores] = {0,1,2,3,5,6}; //Se definen los pines analógicos que se usaran 
const int pines_analogicos_funcion[cantidad_sensores] = {0,0,0,1,1,1}; //Se identifica cada pin como entrada=0; salida=1;
int valores_analogicos[cantidad_sensores]; //Aqui se guardaran los valores que envia o recibe del nodeMCU

const int pines_digitales[cantidad_sensores] = {7,8,9,10,11,12}; //Se definen los pines digitales que se usaran 
const int pines_digitales_funcion[cantidad_sensores] = {0,0,0,1,1,1};//Se identifica cada pin como entrada=0; salida=1;
int valores_digitales[cantidad_sensores]; //Aqui se guardaran los valores que envia o recibe del nodeMCU

unsigned long tiempo_anterior;

/* FUNCIONES UTILITARIAS*/

void leerEntradasAD(){ //Lee las entradas del arduino para luego ser enviadas al nodeMCU
  for(int i = 0; i < cantidad_sensores/2; i++){
    if(pines_digitales_funcion[i] == 0){ //Entradas digitales
      valores_digitales[i] = digitalRead(pines_digitales[i]);
    }

    if(pines_analogicos_funcion[i] == 0){ //Entradas analógicas
      valores_analogicos[i] = analogRead(pines_analogicos[i]);
    }
  }
}

void cambiarEstadoDigital(int i, int estado){//Recibe por parámetros el pin digital y el valor enviado desde el nodeMCU
  if(i >= 3 && i < cantidad_sensores){
    if(pines_digitales_funcion[i] > 0){
      valores_digitales[i] = (estado != 0)?HIGH:LOW ;
      digitalWrite(pines_digitales[i], valores_digitales[i] ); //Activa o desactiva la salida

      if(valores_digitales[i]){
        Serial.print("Activada la salida digital ");Serial.println(i);
      }else{
        Serial.print("Desactivada la salida digital ");Serial.println(i);
      }
    }
  }
}

void escribirSalidaAnalogica(int i, int valor){//Recibe por parámetros el pin analógico y el valor enviado desde el nodeMCU
  if(i >= 3 && i < cantidad_sensores){ 
    if(pines_analogicos_funcion[i] > 0){
      valores_analogicos[i] = constrain(valor,0,255);//Restringe los valores desde 0 hasta 255
      analogWrite(pines_analogicos[i], valores_analogicos[i]);

      Serial.println("Cambiada la salida anal�gica.");
    }
  }
}

/* RESTO DE LAS COSAS DE ARDUINO */

void setup() {
 Serial.begin(9600);

 /* Configuro los pines digitales como entrada o salida 
    dependiendo de lo que me diga el vector de funcion arriba*/
 for(int i = 0; i < cantidad_sensores; i++) //Declara el pin tipo entrada o salida
  if(pines_digitales_funcion[i] > 0)
    pinMode(pines_digitales[i], OUTPUT);
  else
    pinMode(pines_digitales[i], INPUT);

  tiempo_anterior = millis();
}

void loop() {
  if(Serial.available() > 0){
    Serial.print("Lleg� informacion del NodeMCU");
    int indice = Serial.parseInt();
    int valor = Serial.parseInt();

    if(indice >= 3 && indice < 6)
      cambiarEstadoDigital(indice,valor);
    else
      if(indice >= 9 && indice < 12)
        escribirSalidaAnalogica(indice - 6,valor);
  }

  if( (millis() - tiempo_anterior) >= tiempo_muestreo){//Cada 1000 ms envia información al nodeMCU
    for(int i = 0; i < cantidad_sensores; i++){
      Serial.print(i);Serial.print(":");Serial.print(valores_digitales[i]);Serial.print("/");//Envia valores digitales
    }

    for(int i = 0; i < cantidad_sensores; i++){
      Serial.print(i + 6);Serial.print(":");Serial.print(valores_analogicos[i]);Serial.print("/");//Envia valores analógicos
    }

    Serial.println();

    tiempo_anterior = millis();
  }

  leerEntradasAD();//Entradas
}
