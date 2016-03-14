<?php

/*
  |--------------------------------------------------------------------------
  | DEFINICIONES PARA myMoodleService
  |--------------------------------------------------------------------------
  |
  | configuracion del servidor moodle para el consumo de webservices
  |
 */

// path que se va a utilizar, dependiendo del protocolo
define('MDL_WSPATH', '/webservice/xmlrpc/server.php?wstoken=');
define('MDL_USERPREFIX', ''); // prefijo para crear usernames
// url del servidor de desarrollo
define('MDL_WS_LOCALSERVER', 'http://localhost');
define('MDL_WS_LOCALTOKEN', 'your_token');
define('MDL_WS_LOCALDIR', '');


// url de servidor de produccion
define('MDL_WS_REMOTESERVER', 'http://www.yourmoodle.com');
define('MDL_WS_REMOTETOKEN', 'your_token');
define('MDL_WS_REMOTEDIR', '');
?>