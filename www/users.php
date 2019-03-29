<?php
  include("header.php");
  $usersDb = new SQLite3($users_db);

  if (isset($_GET['action'])) {
    if ($_GET['action'] === "delete") {
      $action = "delete";
    } elseif ($_GET['action'] === "add") {
      $action = "add";
    }
  } else {
    $action = "browse";
  }

  if (isset($_GET['groupname'])) {
    if ($_GET['groupname'] === "editors" | $_GET['groupname'] === "admins" | $_GET['groupname'] === "developers") {
      $group_name = $_GET['groupname'];
    }
  }

  if (isset($_GET['principalname'])) {
    $principal_name = $_GET['principalname'];
  }

  if (($action === "delete") && (isset($group_name)) && (isset($principal_name))) {
    $delete = $usersDb->prepare("DELETE FROM group_principal WHERE group_name = :groupname AND principal_name = :principalname");
    $delete->bindValue(':groupname', $group_name, SQLITE3_TEXT);
    $delete->bindValue(':principalname', $principal_name, SQLITE3_TEXT);
    $results = $delete->execute();
  } elseif (($action === "add") && (isset($group_name)) && (isset($principal_name)) && (preg_match('/\\S+@\\S+\\.\\S+/', $principal_name))) {
    $insert = $usersDb->prepare("INSERT OR IGNORE INTO group_principal (group_name, principal_name) VALUES(:groupname, LOWER(:principalname))");
    $insert->bindValue(':groupname', $group_name, SQLITE3_TEXT);
    $insert->bindValue(':principalname', $principal_name, SQLITE3_TEXT);
    $results = $insert->execute();
  }

  $select = $usersDb->prepare("SELECT principal_name FROM group_principal WHERE group_name = :groupname");
  $select->bindValue(':groupname', $group_name, SQLITE3_TEXT);
  $results = $select->execute();

?>

<h3>Current members of group <strong><?php print $group_name ?></strong>:</h3>

<table>
<tr><form>
<td><input type="hidden" name="action" value="add"/><input type="hidden" name="groupname" value="<?php print $group_name ?>"/><input type="text" name="principalname" size="64" maxlength="64" placeholder="Enter an @gmail.com address to authorize"/></td>
<td><input type="submit" value="→" title="Authorize this user"/></td>
</form></tr>
<?php
  $evenOdd = 0;
  while ($row = $results->fetchArray()) {
    $principal_name = $row["principal_name"];
?>
    <tr<?php if ($evenOdd % 2) {?> bgcolor="#c0c0c0" <?php } ?> >
    <td><strong><?php print $principal_name ?></strong></td>
    <td><form><input type="hidden" name="action" value="delete"/><input type="hidden" name="groupname" value="<?php print $group_name ?>"/><input type="hidden" name="principalname" value="<?php print $principal_name ?>"/><button title="Remove this user">&times;</button></form></td>
    </tr>
<?php
    $evenOdd++;
  }
?>
</table>

<?php include("footer.php"); ?>
