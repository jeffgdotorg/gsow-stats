<?php
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  error_reporting(-1);
  include("../config.php");
  $conn = mysqli_connect($host, $user, $password, $dbname, $port);
  $conn->set_charset("utf8");
  $msg = "";

  include("header.php");

  $total_live_page_count = 0;
  $total_page_view_pages = 0;
  $sum_all_language_taggings = 0;
  $sum_all_page_new_rewrite_taggings = 0;

  $q = "SELECT COUNT(*) AS tally FROM edits";
  $result = mysqli_query($conn, $q);
  $row = mysqli_fetch_array($result);
  $total_live_page_count = $row['tally'];

  $q = "SELECT COUNT(DISTINCT pageid) AS tally FROM page_views";
  $result = mysqli_query($conn, $q);
  $row = mysqli_fetch_array($result);
  $total_page_view_pages = $row['tally'];

  $q = "SELECT COUNT(*) AS tally FROM tags WHERE lower(tag) LIKE 'language %'";
  $result = mysqli_query($conn, $q);
  $row = mysqli_fetch_array($result);
  $sum_all_language_taggings = $row['tally'];

  $q = "SELECT COUNT(*) AS tally FROM tags WHERE lower(tag) IN ('page new', 'page rewrite')";
  $result = mysqli_query($conn, $q);
  $row = mysqli_fetch_array($result);
  $sum_all_page_new_rewrite_taggings = $row['tally'];

  #################
  # ANOMALY QUERIES
  #################
  $anomaly_language_count = array();
  $q = "SELECT edit.pageid AS pageid, edit.page AS title FROM (SELECT pageid, COUNT(*) AS tally FROM tags WHERE lower(tag) LIKE 'language %') AS pagetags WHERE pagetags.tally <> 1";
  $result = mysqli_query($conn, $q);
  while ($row = mysqli_fetch_assoc($result)) {
    array_push($anomaly_language_count, printf("<a href=\"admin.php?page=%d\">%s</a>", $row["pageid"], $row["title"]));
  }

  $anomaly_editor_count = array();
  $q = "SELECT edits.pageid AS pageid, edits.page AS title FROM (SELECT pageid, count(*) AS tally FROM tags WHERE lower(tag) LIKE 'editor %' GROUP BY pageid) AS pagetags JOIN edits ON pagetags.pageid = edits.pageid WHERE tally != 1;";
  $result = mysqli_query($conn, $q);
  while ($row = mysqli_fetch_assoc($result)) {
    array_push($anomaly_editor_count, printf("<a href=\"admin.php?page=%d\">%s</a>", $row["pageid"], $row["title"]));
  }

?>
  <h1>Reconciliation Queries</h1>
  <h2>Page counts</h2>
  <table>
  <tbody>
    <tr><th>Query</th><th>Value</th><th>Remarks</th></tr>
    <tr>
      <td>Total live pages</td>
      <td><?php echo $total_live_page_count ?></td>
      <td>Simply counts the rows in the <i>edits</i> table. This number is practically the number of live Wikipedia pages currently tracked by Stat Badger.</td>
    </tr>
    <tr>
      <td>Total distinct page-view pages</td>
      <td><?php echo $total_page_view_pages ?></td>
      <td>Counts the distinct page IDs in the <i>page_views</i> table. This number is expected to be larger than <b>Total live pages</b> because of pages that were deleted from Wikipedia after we fetched some stats for them. Jeff G. refers to such pages as "orphaned pages".</td>
    </tr>
    <tr>
      <td>Sum of all <i>Language *</i> taggings</td>
      <td><?php echo $sum_all_language_taggings ?></td>
      <td>Number of times any tag starting with <i>Language</i>, such as <i>Language English</i> or <i>Language Romanian</i>, is applied. It's possible (but anomalous) for one page to have multiple <i>Language</i> tags, or to have none.</td>
    </tr>
    <tr>
      <td>Sum of all <i>Page New</i> and <i>Page Rewrite</i> taggings</td>
      <td><?php echo $total_live_page_count ?></td></td>
      <td>Number of times the exact tags <i>Page New</i> and <i>Page Rewrite</i> are applied. It's possible (but anomalous) for one page to have both these tags, or to have none.</td>
    </tr>
  </tbody>
  </table>

  <h2>Anomalous pages</h2>
  <table>
  <tbody>
    <tr><th>Query</th><th>Result pages</th><th>Remarks</th></tr>
    <tr>
      <td>Pages not having exactly one <i>Language</i> tag</td>
      <td>
        <?php
        if (count($anomaly_language_count) == 0) {
          echo "--";
        }
        foreach ($anomaly_language_count as $link) {
          echo "<li>$link</li>";
        }
        ?>
      </td>
      <td>Each linked page has either no <i>Language</i> tags, or has multiple <i>Language</i> tags.</td>
    </tr>
    <tr>
      <td>Pages not having exactly one <i>Editor</i> tag</td>
      <td>
        <?php
        if (count($anomaly_editor_count) == 0) {
          echo "--";
        }
        foreach ($anomaly_editor_count as $link) {
          echo "<li>$link</li>";
        }
        ?>
      </td>
      <td>Each linked page has either no <i>Editor</i> tags, or has multiple <i>Editor</i> tags.</td>
    </tr>

  </tbody>
  </table>

<?php
  include("footer.php");
?>
