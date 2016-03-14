<?php
require (filter_input('INPUT_SERVER', '/config/moodle_config.inc'));
require (filter_input('INPUT_SERVER', '/curl.inc'));

/**
 * _____________________________________________________________________________
 * 
 * CLASE QUE CONSUME WEBSERVICES DE MOODLE VIA XML-RPC
 * _____________________________________________________________________________
 * @package myMoodleService
 * @author diEmarC <diemarc.os@gmail.com>
 * @license http://http://www.gnu.org/licenses/gpl-3.0.en.html
 * @version 1.0
 * 
 * _____________________________________________________________________________
 */
class MyMoodleService
{

    protected
    // token del webservice del moodle
            $_token,
            // servidor del webservice
            $_server,
            // directorio en el q se encuentra el moodle
            $_dir,
            // errores
            $_error,
            // full path server
            $_server_path,
            // webservice path
            $_server_ws;
    private $_curl;

    /*
      |-------------------------------------------------------------------------
      | Constructor
      |-------------------------------------------------------------------------
      |
      | En el cosntructutor creamos el objeto CURL para procesar los POST, y
      | definimos el entorno desitno, (loca,remoto)
     */

    public function __construct($env)
    {
        $this->_curl = new curl();

        // seteamos el entorno destino
        $this->_setEnvironment($env);

        // montamos el full path del server
        $this->_server_path = $this->_server . $this->_dir . $this->_server_ws . $this->_token;
    }

    /*
      |_________________________________________________________________________
      |
      | 0-METODOS PRIVADOS PARA LA EJECUCION DEL WEBSERVICE
      |_________________________________________________________________________
     */

    /**
     * -------------------------------------------------------------------------
     * 0.1- Setea los parametros de acuerdo al entorno
     * -------------------------------------------------------------------------
     * (local = ; remote = 
     * Utiliza cosntantes definidas en 
     * /../core/app/external/moodle/config/moodle_config.inc
     * -------------------------------------------------------------------------
     * @param type $env
     */
    private function _setEnvironment($env)
    {

        // seteamos el directorio del web service
        $this->_server_ws = MDL_WSPATH;


        switch ($env) {

            case 'local';
                $this->_server = MDL_WS_LOCALSERVER;
                $this->_token = MDL_WS_LOCALTOKEN;
                $this->_dir = MDL_WS_LOCALDIR;

                break;
            case 'remote';
                $this->_server = MDL_WS_REMOTESERVER;
                $this->_token = MDL_WS_REMOTETOKEN;
                $this->_dir = MDL_WS_REMOTEDIR;

                break;
        }
    }

    /**
     * -------------------------------------------------------------------------
     * Postea un xml al servidor
     * -------------------------------------------------------------------------
     * @param type $request
     */
    private function _postRequest($request)
    {
        return xmlrpc_decode($this->_curl->post($this->_server_path, $request));
    }

    /*
      |_________________________________________________________________________
      |
      | A- GESTION DE USUARIOS
      |_________________________________________________________________________
      |
     */

    /**
     * --------------------------------------------------------------------------
     * A.1- Obtiene un usuario mediante algun campo
     * los campos pueden ser (id,username,email)
     * ------------------------------------------------------------------------- 
     * @param string $field el campo que queremos usar para buscar un usuario
     * @param array $values array que contiene el elemento a buscar
     * @return xml response xml que responde el servidor
     * @throws Exception
     */
    public function getUserMoodle($field, $values)
    {
        $params = array($field, array($values));
        $request = xmlrpc_encode_request('core_user_get_users_by_field', $params, array('encoding' => 'UTF-8'));
        $response = $this->_postRequest($request);

        //print_r($response);
        if (!is_array($response)) {
            throw new Exception;
        } else {
            return $response;
        }
    }

    /**
     * -------------------------------------------------------------------------
     * A.2- Agrega un usuario a moodle
     * -------------------------------------------------------------------------
     * @param array $campos array que contiene los campos de a insertar
     * @return boolean
     */
    public function addUserMoodle($campos)
    {

        // primero comprobamos
        $this->checkParamsUserMoodle($campos);

        // campos a procesar
        $user_fields = array();
        $user_fields['username'] = (isset($campos['username'])) ? $campos['username'] : '';
        $user_fields['password'] = (isset($campos['password'])) ? $campos['password'] : '';
        $user_fields['firstname'] = (isset($campos['firstname'])) ? $campos['firstname'] : '';
        $user_fields['lastname'] = (isset($campos['lastname'])) ? $campos['lastname'] : '';
        $user_fields['email'] = (isset($campos['email'])) ? $campos['email'] : '';
        $user_fields['city'] = (isset($campos['city'])) ? $campos['city'] : '';
        $user_fields['country'] = (isset($campos['country'])) ? $campos['country'] : '';
        //$user_fields['preferences'] = (isset($campos['preferences'])) ? $campos['preferences'] : '';

        $request = xmlrpc_encode_request('core_user_create_users', array(array($user_fields)), array('encoding' => 'UTF-8'));
        $response = $this->_postRequest($request);

        // print_r($response);

        if ($response[0]) {
            echo "usuario insertado correctamente, vuelva plonto";
            return true;
        } else {
            echo "hay algun error " . print_r($response);
        }
    }

    /**
     * -------------------------------------------------------------------------
     * A.3-Comprueba si el usuario/email ya esta dado de alta
     * -------------------------------------------------------------------------
     * @param type $campos
     */
    public function checkParamsUserMoodle($campos)
    {

        $mdl_user = $campos['username'];
        $mdl_email = $campos['email'];

        // primero comprobamos que el usuario/email no exista
        $mdl_check_user = $this->getUserMoodle('username', $mdl_user);
        $mdl_check_email = $this->getUserMoodle('email', $mdl_email);

        if (count($mdl_check_user) > 0) {
            echo "El usuario $mdl_user ya existe dado de alta, cambialo, vuelva plonto...";
            die();
        }

        if (count($mdl_check_email) > 0) {
            echo "El email $mdl_email ya existe dado de alta, cambialo, vuelva plonto...";
            die();
        }
    }

    /**
     * -------------------------------------------------------------------------
     * Genera usuario apartir de un keyword
     * -------------------------------------------------------------------------
     * @param type $keyname
     */
    private function _generateUserNameMoodle($keyname)
    {
      
        return MDL_USERPREFIX."_".$keyname;
        
    }

    /*
      |_________________________________________________________________________
      |
      | B-METODOS PARA GESTIONAR CURSOS
      |_________________________________________________________________________
      |
     */

    /**
     * -------------------------------------------------------------------------
     * B.0- Obtiene todos los cursos
     * -------------------------------------------------------------------------
     */
    public function getAllCourses()
    {

        $params = array();
        $request = xmlrpc_encode_request('core_course_get_courses', $params, array('encoding' => 'UTF-8'));
        $response = $this->_postRequest($request);

        return $response;
    }

    /**
     * --------------------------------------------------------------------------
     * B.1- Inscribe un alumno a un curso
     * ------------------------------------------------------------------------- 
     * @param int $id_user el id de usuario
     * @param int $id_course id curso
     * @param int $id_role el rol a asignar (5= estudiante)
     */
    public function enrolUserCourseMoodle($id_user, $id_course, $id_role = 5)
    {

        $params = array(
            array(
                array(
                    'roleid' => $id_role,
                    'userid' => $id_user,
                    'courseid' => $id_course
                )
            )
        );
        $request = xmlrpc_encode_request('enrol_manual_enrol_users', $params, array('encoding' => 'UTF-8'));
        $response = $this->_postRequest($request);

        print_r($response);
    }

}
