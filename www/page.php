<?php
  ini_set('display_errors',1);
  ini_set('display_startup_errors',1);
  include("header.php");
  include("../config.php");
  $conn = mysqli_connect($host, $user, $password, $dbname, $port);
  $conn->set_charset("utf8");
  if (isset($_POST['act'])) {
    if ($_POST['act'] == 'add') {
      if (isset($_POST['tags_language'])) {
        foreach ($_POST['tags_language'] as $value) {
          $q = "INSERT INTO tags (tag, pageid) VALUES ('" . trim($value) . "', " . $_POST['pageid'] . ");";
          $result = mysqli_query($conn, $q);
          $q = "SELECT tag FROM tag_group WHERE tag_group = 'Language' AND tag = '" . trim($value) . "';";
          $result = mysqli_query($conn, $q);
          if (sizeof(mysqli_fetch_array($result)) == 0) {
            $q = "INSERT INTO tag_group (tag_group, tag) VALUES('Language', '" . trim($value) . "');";
            $result = mysqli_query($conn, $q);
          }
        }
      }
      if (isset($_POST['tags_editor'])) {
        foreach ($_POST['tags_editor'] as $value) {
          $q = "INSERT INTO tags (tag, pageid) VALUES ('" . trim($value) . "', " . $_POST['pageid'] . ");";
          $result = mysqli_query($conn, $q);
          $q = "SELECT tag FROM tag_group WHERE tag = '" . trim($value) . "';";
          $result = mysqli_query($conn, $q);
          if (sizeof(mysqli_fetch_array($result)) == 0) {
            $q = "INSERT INTO tag_group (tag_group, tag) VALUES('Editors', '" . trim($value) . "');";
            $result = mysqli_query($conn, $q);
          }
        }
      }
      if (isset($_POST['tags_gsow'])) {
        foreach ($_POST['tags_gsow'] as $value) {
          $q = "INSERT INTO tags (tag, pageid) VALUES ('" . trim($value) . "', " . $_POST['pageid'] . ");";
          $result = mysqli_query($conn, $q);
          $q = "SELECT tag FROM tag_group WHERE tag = '" . trim($value) . "';";
          $result = mysqli_query($conn, $q);
          if (sizeof(mysqli_fetch_array($result)) == 0) {
            $q = "INSERT INTO tag_group (tag_group, tag) VALUES('GSOW', '" . trim($value) . "');";
            $result = mysqli_query($conn, $q);
          }
        }
      }
      if (isset($_POST['tags_region'])) {
        foreach ($_POST['tags_region'] as $value) {
          $q = "INSERT INTO tags (tag, pageid) VALUES ('" . trim($value) . "', " . $_POST['pageid'] . ");";
          $result = mysqli_query($conn, $q);
          $q = "SELECT tag FROM tag_group WHERE tag = '" . trim($value) . "';";
          $result = mysqli_query($conn, $q);
          if (sizeof(mysqli_fetch_array($result)) == 0) {
            $q = "INSERT INTO tag_group (tag_group, tag) VALUES('Region', '" . trim($value) . "');";
            $result = mysqli_query($conn, $q);
          }
        }
      }
      if (isset($_POST['tags_misc'])) {
        foreach ($_POST['tags_misc'] as $value) {
          $q = "INSERT INTO tags (tag, pageid) VALUES ('" . trim($value) . "', " . $_POST['pageid'] . ");";
          $result = mysqli_query($conn, $q);
          $q = "SELECT tag FROM tag_group WHERE tag = '" . trim($value) . "';";
          $result = mysqli_query($conn, $q);
          if (sizeof(mysqli_fetch_array($result)) == 0) {
            $q = "INSERT INTO tag_group (tag_group, tag) VALUES('', '" . trim($value) . "');";
            $result = mysqli_query($conn, $q);
          }
        }
      }
    }
    else {
      $q = "DELETE FROM tags where tag_id = " . $_POST['tag_id'];
      $result = mysqli_query($conn, $q);
    }
    $pageid = $_POST['pageid'];
  }
  else {
    $pageid = $_GET['pageid'];
  }
  $query = "SELECT edit_id, lang, page, start, pageid " .
           "FROM edits " .
           "WHERE pageid=" . $pageid;
  $result = mysqli_query($conn, $query);
  while ($r = mysqli_fetch_array($result)) {
    $row = $r;
    $title = $row['page'];
    $lang = $row['lang'];
  }
  $query = "SELECT tag_id, tag from tags WHERE pageid=" . $pageid;
  $result = mysqli_query($conn, $query);
  $tags = array();
  while ($r = mysqli_fetch_array($result)) {
    $e = array("tag" => $r['tag'], "tag_id" => $r['tag_id']);
    array_push($tags, $e);
  }
  $query = "SELECT title, min(ts) as mindt, max(ts) as maxdt, count(distinct editor_id) as editors, sum(1) as edits " .
           "FROM contributions " .
           "WHERE pageid=" . $pageid . " " .
           "GROUP BY title";
  $query = "SELECT * from edits WHERE pageid=" . $pageid;
  $result = mysqli_query($conn, $query);
  while ($r = mysqli_fetch_array($result)) {
    $row = $r;
  }
  $result = mysqli_query($conn, "SELECT t1.name, sum(1) as c FROM editor t1 JOIN contributions t2 on t1.editor_id = t2.editor_id WHERE t2.pageid=25867");
  $editors = array();
  while ($r = mysqli_fetch_array($result)) {
    $e = array("name" => $r['name'], "c" => $r['c']);
    array_push($editors, $e);
  }
  $result = mysqli_query($conn, "select sum(views) as total_views from page_views where pageid=" . $pageid . " and dt >= (select start from edits where pageid=" . $pageid . ")");
  while ($r = mysqli_fetch_array($result)) {
    $pvs = $r['total_views'];
  }
?>

<h1><?php echo($title); ?></h1>
<a target="_blank" href="https://<?php echo($lang); ?>.wikipedia.org/wiki/<?php echo($title); ?>">wiki page</a>

<table>
  <tr>
    <td>Total pageviews since first GSoW edit:</td>
    <td><b><?php echo($pvs); ?></b></td>
  </tr>
  <tr>
    <td>First GSoW edit:</td>
    <td><?php echo(substr($row['start'], 0, 10)); ?></td>
  </tr>
</table>

<style>
.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
  font: 10px sans-serif;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}
</style>

<script>
var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.timeFormat("%Y-%m-%d").parse;

var x = d3.scaleTime()
    .range([0, width]);

var y = d3.scaleLinear()
    .range([height, 0]);

var xAxis = d3.select(".axis")
    .call(d3.axisBottom(x));

var yAxis = d3.select(".axis")
    .call(d3.axisLeft(y));

var line = d3.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.views); });

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.tsv("page-data.php?pageid=<?php echo($pageid); ?>", function(error, data) {
  if (error) throw error;

  data.forEach(function(d) {
    //d.date = parseDate(d.date);
    d.date = Date.parse(d.date);
    d.views = +d.views;
  });

  x.domain(d3.extent(data, function(d) { return d.date; }));
  y.domain(d3.extent(data, function(d) { return d.views; }));

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x));

  svg.append("g")
      .attr("class", "y axis")
      .call(d3.axisLeft(y))
      .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Page Views");

  svg.append("path")
      .datum(data)
      .attr("class", "line")
      .attr("d", line);
});

</script>

<script>
  $(function() {
    $('#tags_language').magicSuggest({
        "data": "get_tags_by_group.php?taggroup=Language",
        "name": "tags_language[]",
        "placeholder": "Language"
    });
    $('#tags_editor').magicSuggest({
        "data": "get_tags_by_group.php?taggroup=Editors",
        "name": "tags_editor[]",
        "placeholder": "Editor"
    });
    $('#tags_gsow').magicSuggest({
        "data": "get_tags_by_group.php?taggroup=GSOW",
        "name": "tags_gsow[]",
        "placeholder": "GSOW tags"
    });
    $('#tags_region').magicSuggest({
        "data": "get_tags_by_group.php?taggroup=Region",
        "name": "tags_region[]",
        "placeholder": "Region tags"
    });
    $('#tags_misc').magicSuggest({
        "data": "get_tags_by_group.php?taggroup=",
        "name": "tags_misc[]",
        "placeholder": "Misc tags"
    });
  });
</script>

<hr/>

<center>
<form action='page.php' method='POST'>
  Add tags:
  <input type='hidden' name='pageid' value='<?php echo($pageid); ?>' />
  <input type='hidden' name='act' value='add' />
  <div id="tags_language" style="width: 16em"></div>
  <div id="tags_editor" style="width: 16em"></div>
  <div id="tags_gsow" style="width: 16em"></div>
  <div id="tags_region" style="width: 16em"></div>
  <div id="tags_misc" style="width: 16em"></div>
  <input type='submit' value='Add' />
</form>
</center>
<br/>


<table id="myTable" class="tablesorter">
  <thead>
    <tr>
      <th>Keyword</th>
      <th>Delete</th>
    </tr>
  </thead>
  <tbody>
  <?php
    foreach ($tags as $tag) {
      echo("<tr><td><a href='index.php?tag=" . $tag['tag'] . "'>" . $tag['tag'] . "</a></td>");
      echo("<td><form action='page.php' method=POST style='display: inline'>");
      echo("<input type='hidden' name='tag_id' value='" . $tag['tag_id'] . "' />");
      echo("<input type='hidden' name='pageid' value='" . $pageid . "' />");
      echo("<input type='hidden' name='act' value='delete' />");
      echo("<input type='submit' value='Delete' /></form></td></tr>");
    }
  ?>
  </tbody>
</table>

<?php include("footer.php"); ?>
