<?php

require 'settings.php';
require 'global.php';

/**
 * Local Composer
 */
require 'vendor/autoload.php';

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
    <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css"
          integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, Verdana;
            padding: 30px
        }

        label {
            margin-top: 50px;
            width: 360px;
            font-size: 22px;
            font-weight: 700;
            padding-bottom: 10px;
        }

        input {
            margin-top: 5px;
            height: 40px;
            width: 400px;
            font-size: 20px;
            padding-left: 15px;
            text-transform: uppercase;
            vertical-align: bottom;
        }

        button {
            color: #fff;
            vertical-align: bottom;
            height: 46px;
        }

        .button-success {
            background: #1cb841;
            color: #fff;
        }

        .button-secondary {
            background: #42b8dd
        }

        .button-small {
            font-size: 85%
        }

        .button-xlarge {
            font-size: 125%
        }</style>
</head>
<body>
<?php if (!empty($error)) { ?>
    <div>
        <p><?php echo $error ?></p>
    </div>
<?php } ?>

<form method="POST">
    <div>
        <label>Periode
            <select name="period">
                <option value="vandaag">Vandaag</option>
                <option value="gisteren">Gisteren</option>
                <option value="week">Deze week</option>
                <option value="sprint">Deze sprint</option>
            </select>
        </label>
        <button name="submit" class="button-success button-xlarge pure-button" value="fetch">Run Report</button>
    </div>
</form>

<?php if (!empty($rows)) { ?>
    <hr/>
    <table class="pure-table pure-table-bordered">
        <thead>
        <tr>
            <th width="150">Key</th>
            <th width="150">Date</th>
            <th>Total Time Spent (min.)</th>
            <th>Total Time Spent (hrs.)</th>
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

                if ($teller != 0) {
                    ?>
                    <tr>
                    <td></td>
                    <?php
                }
                ?>

                <td><?php echo $date; ?></td>

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
            <td>Totaal</td>
            <td><?php echo round($total_minutes, 2); ?></td>
            <td><?php echo round($total_minutes / 60, 2); ?></td>
        </tr>

        </tbody>
    </table>
    <?php
}
?>

</body>
</html>