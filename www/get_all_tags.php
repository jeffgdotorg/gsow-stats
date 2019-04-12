<?php
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  include("../config.php");
  $conn = mysqli_connect($host, $user, $password, $dbname, $port);
  $conn->set_charset("utf8");
  $query = "SELECT DISTINCT tag FROM tags";
  $result = mysqli_query($conn, $query);
  $tags = array();
  while ($r = mysqli_fetch_array($result)) {
    array_push($tags, $r['tag']);
  }

  echo(json_encode($tags));
