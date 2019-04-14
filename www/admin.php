<?php
  include("header.php");
  $conn = mysqli_connect($host, $user, $password, $dbname, $port);
  $conn->set_charset('utf8');
  $page = "";
  $dt = "YYYY-MM-DD";
  $act = "Add";
  $lang = "";
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $dt = substr($_GET['dt'], 0, 10);
    $lang = $_GET['lang'][0];
    $act = "Update";
  }
  if (isset($_POST['page'])) {
    $page = $_POST['page'];
    $lang = $_POST['lang'][0];
    if ($lang == "") {
      echo("<h2><i class='fas fa-exclamation-triangle'></i>Please choose a valid language</h2>");
    }
    elseif (trim($_POST['page']) == "") {
      echo("<h2><i class='fas fa-exclamation-triangle'></i>Sorry, you need to enter a page name</h2>");
    }
    else {
      if ((! isset($_POST['dt'])) || ($_POST['dt'] == "") || ($_POST['dt']=="YYYY-MM-DD")) {
        echo("<h2><i class='fas fa-exclamation-triangle'></i>Please enter a valid date</h2>");
      }
      else {
        $dt = $_POST['dt'];
        $lang = $_POST['lang'][0];
        $page = str_replace("'", "\'", $page);
        $query = "UPDATE edits SET start='$dt' WHERE page='" . trim($page) . "' and lang='$lang'";
        $result = mysqli_query($conn, $query);
        $rows = mysqli_affected_rows($conn);
        if ($rows == 1) {
          echo("<h2>Page updated</h2>");
        }
        else {
          $query = "INSERT INTO edits (page, lang, start) VALUES ('" . trim($page) . "', '$lang', '$dt');";
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
    $lang = "";
  }
?>

<script>
  $(function() {
    $("#datepicker").datepicker({
      "dateFormat": "yy-mm-dd",
      "changeMonth": true,
      "changeYear": true,
      "showOtherMonths": true,
      "selectOtherMonths": true
    });
  });
</script>
<script>
  $(function() {
    $('#langpicker').magicSuggest({
        "allowFreeEntries": false,
        "data": "/assets/iso-639-1_codes.json",
        "maxSelection": 1,
        "name": "lang",
        "placeholder": "<?php if ($lang != "") { echo($lang); } else { echo "Language"; } ?>",
        "resultAsString": true,
        "value": ["<?php if ($lang != "") { echo($lang); } else { echo "en"; } ?>"],
        "disabled": <?php echo($lang != "" ? "true" : "false"); ?>,
    });
  });
</script>

<form action='admin.php' method=POST>
<h1><?php echo($act); ?> a page</h1>
<table>
  <tr>
    <td>Page name:</td>
    <td><input name='page' autocomplete="off" value="<?php echo($page); ?>" /></td>
  </tr>
  <tr>
    <td>Language:</td>
    <td>
      <div id="langpicker" style="width: 16em;"></div>
      <?php if ($lang != "") {?><input type="hidden" name="lang[]" value="<?php echo $lang ?>"/><?php } ?>
    </td>
  </tr>
  <tr>
    <td>First major edit date:</td>
    <td><input name="dt" id='datepicker' autocomplete="off" placeholder="YYYY-MM-DD" value="<?php if ($dt != 'YYYY-MM-DD') { echo $dt; } ?>"</input></td>
  </tr>
  <tr>
    <td colspan=2 align=right><input style='width:100%' type='submit' value='<?php echo($act); ?>' /></td>
  </tr>
</table>
</form>

<?php include("footer.php"); ?>
