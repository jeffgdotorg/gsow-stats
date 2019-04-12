<?php
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  include("../config.php");
  $tags = array();
  if (isset($_GET['taggroup'])) {
    $conn = new mysqli($host, $user, $password, $dbname, $port);
    $conn->set_charset("utf8");
    $sth = $conn->prepare("SELECT DISTINCT tag FROM tags WHERE tag IN (SELECT DISTINCT tag FROM tag_group WHERE tag_group = ?) ORDER BY tag ASC");
    $sth->bind_param("s", $_GET['taggroup']);
    $sth->execute();
    $result = $sth->get_result();
    while ($r = $result->fetch_assoc()) {
      array_push($tags, $r['tag']);
    }
  }

  echo(json_encode($tags));
