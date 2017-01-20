<?php
  include("header.php");
  $conn = mysqli_connect($host, $user, $password, "gsow");
  $page = "";
  $dt = "YYYY-MM-DD";
  $act = "Add";
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $dt = substr($_GET['dt'], 0, 10);
    $act = "Update";
  }
  if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if (!isset($_POST['dt'])) {
      echo("<h2>Sorry, you need to enter a start date</h2>");
    }
    else {
      if ($_POST['dt']=="YYYY-MM-DD") {
        echo("<h2>Please enter a valid date</h2>");
      }
      else {
        $dt = $_POST['dt'];
        $query = "UPDATE edits SET start='$dt' WHERE page='$page'";
        $result = mysqli_query($conn, $query);
        $rows = mysqli_affected_rows($conn);
        if ($rows == 1) {
          echo("<h2>Page updated</h2>");
        }
        else {
          $query = "INSERT INTO edits (page, start) VALUES ('$page', '$dt');";
          $result = mysqli_query($conn, $query);
          $rows = mysqli_affected_rows($conn);
          if ($rows != 1) {
            echo("<h2>Sorry, there was an error adding that page</h2>");
          }
          else {
            echo("<p>Page added successfully. Please allow a few minutes for the data to be downloaded.</p>");
          }
        }
      }
    }
  }
?>
<form action='admin.php' method=POST>
<h1><?php echo($act); ?> page</h1>
<table>
  <tr>
    <td>Page name:</td>
    <td><input name='page' value="<?php echo($page); ?>" /></td>
  </tr>
  <tr>
    <td>First major edit date (english):</td>
    <td><input name='dt' value="<?php echo($dt); ?>" /></td>
  </tr>
  <tr>
    <td colspan=2 align=right><input type='submit' value='<?php echo($act); ?>' /></td>
  </tr>
</table>
</form>

<?php include("footer.php"); ?>
