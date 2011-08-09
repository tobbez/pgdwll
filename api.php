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

    $sql = "SELECT * FROM " . $_SERVER['DB_NAME'] . ".messages ORDER BY `timestamp` DESC LIMIT " . $wanted . ";";
    foreach($dbconn->query($sql, PDO::FETCH_ASSOC) as $row) {
      $result[] = $row;
    }
  } catch (PDOException $e) {
    return_error($e->getMessage());
  }

  echo json_encode(array('status'=> 'success', 'result' => $result));
};
/* args: `message` (required) */
$API_METHODS['/add'] = function($args) {
  if (!array_key_exists('message', $args)) {
    return_error("No `message` specified");
  }

  try {
    $dbconn = new PDO("mysql:" . $_SERVER['DB_HOST'] . ";dbname=" . $_SERVER['DB_NAME'] . ";unix_socket=" . $_SERVER['DB_SOCKET'], $_SERVER['DB_USER'], $_SERVER['DB_PASSWORD']);
    $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO " . $_SERVER['DB_NAME'] . ".messages (message) VALUES (" . $dbconn->quote($args['message']) . ");";
    $dbconn->exec($sql);
  } catch (PDOException $e) {
    return_error($e->getMessage());
  }

  echo json_encode(array('status' => 'success'));
};
/* list methods */
$API_METHODS['/'] = function($args) {
  global $API_METHODS;

  $methods = array_keys($API_METHODS);
  $result = array('status' => 'success', 'result' => $methods);

  echo json_encode($result);
};

if (array_key_exists($_GET['method'], $API_METHODS)) {
  $args = json_decode(file_get_contents("php://input"), TRUE);
  if (is_null($args)) {
    $args = array();
  }
  $API_METHODS[$_GET['method']]($args);
} else {
  return_error("No such API method");
}
