--Pasos para utilizar la clase --

--------------------------------------------------------------------------------
1-Abrir el archivo /config/moodle_config.inc
--------------------------------------------------------------------------------

La clase soporta dos entorno, 
    1- de prueba (CONSTANTES CON PREFIJO MDL_WS_LOCAL)
    2- desarrollo (CONSTANTES CON PREFIJO MDL_WS_REMOTE)

    MDL_WS_LOCALTOKEN = el token

Si se quiere realizar pruebas en un entorno desarrollo, es opcional, 
seleccionar una de ellas.

MDL_WSPATH = el path donde se eloja el servidor de webservice
MDL_USERPREFIX = opcional , si se quiere generar usernames

--------------------------------------------------------------------------------
2- Implementacion
--------------------------------------------------------------------------------

Desde una clase/funcion/file crear un objeto de MyMoodleService

Mirar el archivo index.php

