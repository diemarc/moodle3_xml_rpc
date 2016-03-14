<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "myMoodleService.php");

echo "------------------------------------------------------------------------<br>";
echo "               ENTORNO DE PRUEBAS PARACONSUMIR WEBSERVICE ";
echo "-------------------------------------------------------------------------<br>";
// creamos un objeto moodle
$moodle = new MyMoodleService('local');

//1-crea usuario
// definimos los campos del usuario a guardar
$campos = array(
  'username' => 'username',
  'password' => '_password_',
  'firstname' => 'firstname',
  'lastname' => 'lastname',
  'email' => 'email@domain.com',
  'city' => 'City',
  'country' => 'CO'
);
$user = $moodle->addUserMoodle($campos);

//2- Obtenemos el valor de un usuario
$user_info = $moodle->getUserMoodle('email', 'email@domain.com');
print_r($user_info);

//3- inscribimos el usuario(5) a un curso(2)
$user_course = $moodle->enrolUserCourseMoodle(6, 2);
// 4- recibimos todos los cursos

$courses = $moodle->getAllCourses();