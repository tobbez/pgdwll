<?php

function return_error($errmsg) {
  echo json_encode(array('status' => 'fail', 'status_message' => $errmsg));
  exit(0);
}

$API_METHODS = array();

/* args: `wanted` (optional, default 10) */
$API_METHODS['retrieve'] = function($args) {
};
/* args: `text` (required) */
$API_METHODS['add'] = function($args) {
};
/* list methods */
$API_METHODS[''] = function($args) {
  global $API_METHODS;

  $methods = array_keys($API_METHODS);
  $result = array('status' => 'success', 'result' => $methods);

  echo json_encode($result);
};

if (array_key_exists($_GET['method'], $API_METHODS)) {
  $API_METHODS[$_GET['method']]($_POST);
} else {
  return_error("No such API method");
}