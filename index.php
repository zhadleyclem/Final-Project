<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("ADAY", (60 * 60 * 24));
if ((!isset($_POST['month'])) || (!isset($_POST['year']))) {
    $nowArray = getdate();
    $month = $nowArray['mon'];
    $year = $nowArray['year'];
} else {
    $month = $_POST['month'];
    $year = $_POST['year'];
}
$start = mktime(12, 0, 0, $month, 1, $year);
$firstDayArray = getdate($start);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo "Calendar: " . $firstDayArray['month'] . " " . $firstDayArray['year']; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Select a Month/Year Combination</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <select name="month">
            <?php
            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            for ($x = 1; $x <= count($months); $x++) {
                echo "<option value=\"$x\"";
                if ($x == $month) echo " selected";
                echo ">" . $months[$x - 1] . "</option>";
            }
            ?>
        </select>
        <select name="year">
            <?php
            for ($x = 1990; $x <= 2025; $x++) {
                echo "<option";
                if ($x == $year) echo " selected";
                echo ">$x</option>";
            }
            ?>
        </select>
        <button type="submit" name="submit" value="submit">Go!</button>
    </form>
    <br>
    <?php
    $days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    echo "<table><tr>\n";
    foreach ($days as $day) {
        echo "<th>" . $day . "</th>\n";
    }
    for ($count = 0; $count < (6 * 7); $count++) {
        $dayArray = getdate($start);
        if (($count % 7) == 0) {
            if ($dayArray['mon'] != $month) {
                break;
            } else {
                echo "</tr><tr>\n";
            }
        }
        if ($count < $firstDayArray['wday'] || $dayArray['mon'] != $month) {
            echo "<td>&nbsp;</td>\n";
        } else {
            $event_title = "";
            $mysqli = mysqli_connect("localhost", "u833291163_zhadley", "Zhadley123@", "u833291163_event_app");
            if (!$mysqli) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $chkEvent_sql = "SELECT event_title FROM calendar_events WHERE month(event_start) = '$month' AND dayofmonth(event_start) = '" . $dayArray['mday'] . "' AND year(event_start) = '$year' ORDER BY event_start";
            $chkEvent_res = mysqli_query($mysqli, $chkEvent_sql);
            if ($chkEvent_res === false) {
                echo "<td>Error querying events: " . mysqli_error($mysqli) . "</td>\n";
            } else {
                if (mysqli_num_rows($chkEvent_res) > 0) {
                    while ($ev = mysqli_fetch_array($chkEvent_res)) {
                        $event_title .= stripslashes($ev['event_title']) . "<br>";
                    }
                }
                echo "<td><a href=\"javascript:eventWindow('event.php?m=$month&&d=" . $dayArray['mday'] . "&&y=$year');\">" . $dayArray['mday'] . "</a><br>" . $event_title . "</td>\n";
            }
            unset($event_title);
            $start += ADAY;
            mysqli_close($mysqli);
        }
    }
    echo "</tr></table>";
    ?>
    <script type="text/javascript">
        function eventWindow(url) {
            event_popupWin = window.open(url, 'event', 'resizable=yes, scrollbars=yes, toolbar=no, width=400, height=400');
            event_popupWin.opener = self;
        }
    </script>
</body>
</html>
