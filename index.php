<?php

require 'settings.php';
require 'global.php';

/**
 * Local Composer
 */

include "src/autoload.php";

session_start();
$error = "";

if (!empty($_POST)) {

    if ($_POST["submit"] === "fetch") {
        $jiraKey = strtoupper($_POST["jira_key"]);
        $period = $_POST['period'];
        $result = getData($jiraKey, $period);
        $decodedData = json_decode($result, true);
        $rows = buildRowFromData($result);
        $_SESSION['export'] = $rows;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JIRA Time overview</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="src/bootstrap/dist/css/bootstrap.css">
    <!-- Optional Bootstrap theme -->
    <link rel="stylesheet" href="src/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div class="container">
    <div class="row">
        <?php if (!empty($error)) { ?>
            <div class="col-sm-6">
                <p><?php echo $error ?></p>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form method="POST">
                <div>
                    <label>Period:
                        <select name="period">
                            <option value="today" <?php echo $_POST['period'] == 'today' ? 'selected' : ''; ?>>
                                Today
                            </option>
                            <option value="yesterday" <?php echo $_POST['period'] == 'yesterday' ? 'selected' : ''; ?>>
                                Yesterday
                            </option>
                            <option value="week" <?php echo $_POST['period'] == 'week' ? 'selected' : ''; ?>>Current week
                            </option>
                            <option value="sprint" <?php echo $_POST['period'] == 'sprint' ? 'selected' : ''; ?>>This sprint
                            </option>
                        </select>
                    </label>
                    <button name="submit" class="button-success button-xlarge pure-button" value="fetch">Run Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($rows)) { ?>
    <div class="row">
        <hr/>
        <div class="col-sm-8">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="150">Key</th>
                    <th width="150">Date</th>
                    <th width="150">Total Time Spent (min.)</th>
                    <th width="150">Total Time Spent (hrs.)</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $total_minutes = 0;
                $total_hours = 0;

                foreach ($rows as $index => $row) {
                    $minutes = 0;
                    $teller = 0;
                    ?>

                    <tr>
                    <td><a href="<?php echo $cfg['jira_host_address']; ?>/browse/<?php echo $index; ?>"
                           target="_blank"><?php echo $index; ?></a></td>

                    <?php
                    foreach ($row['entry'] as $date => $entry) {

                        $newDate = new \DateTime($date);
                        $rowDate = $newDate->format('d-m-Y');

                        if ($teller != 0) {
                            ?>
                            <tr>
                            <td></td>
                            <?php
                        }
                        ?>

                        <td><?php echo $rowDate; ?></td>

                        <?php
                        $entryMinutes = 0;
                        foreach ($entry as $time) {
                            $entryMinutes = $entryMinutes + $time['minutes'];
                        }
                        ?>
                        <td><?php echo $entryMinutes; ?></td>
                        <td><?php echo round($entryMinutes / 60, 2); ?></td>
                        </tr>

                        <?php
                        $total_minutes = $total_minutes + $entryMinutes;
                        $teller++;
                    }
                    ?>
                    <?php
                }
                ?>

                <tr>
                    <td></td>
                    <td>Total: </td>
                    <td><?php echo round($total_minutes, 2); ?></td>
                    <td><?php echo round($total_minutes / 60, 2); ?></td>
                </tr>

                </tbody>
            </table>
        </div>

    </div>
<?php } ?>

</div>



<script src="src/jquery/dist/jquery.min.js"></script>
<script src="src/bootstrap/dist/js/bootstrap.min.js"></script>

</body>
</html>