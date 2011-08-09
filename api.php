<?php

function return_error($errmsg) {
  echo json_encode(array('status' => 'fail', 'status_message' => $errmsg));
  exit(0);
}

$API_METHODS = array();

/* args: `wanted` (optional, default 10) */
$API_METHODS['/retrieve'] = function($args) {
  $wanted = 10;
  if (array_key_exists ('wanted', $args) && is_numeric($args['wanted'])) {
    $wanted = intval($args['wanted']);
  }

  $result = array();
  try {
    $dbconn = new PDO("mysql:" . $_SERVER['DB_HOST'] . ";dbname=" . $_SERVER['DB_NAME'] . ";unix_socket=" . $_SERVER['DB_SOCKET'], $_SERVER['DB_USER'], $_SERVER['DB_PASSWORD']);
    $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM " . $_SERVER['DB_NAME'] . ".messages LIMIT " . $wanted . ";";
    foreach($dbconn->query($sql) as $row) {
      $result[] = $row;
    }
  } catch (PDOException $e) {
    return_error($e->getMessage());
  }

  return json_encode($result);
};
/* args: `text` (required) */
$API_METHODS['/add'] = function($args) {
};
/* list methods */
$API_METHODS['/'] = function($args) {
  global $API_METHODS;

  $methods = array_keys($API_METHODS);
  $result = array('status' => 'success', 'result' => $methods);

  echo json_encode($result);
};

if (array_key_exists($_GET['method'], $API_METHODS)) {
  $API_METHODS[$_GET['method']](json_decode(file_get_contents("php://input"), TRUE));
} else {
  return_error("No such API method");
}