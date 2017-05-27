<?php

require 'settings.php';
require 'includes/global.php';

if (!empty($_POST) && $_POST["submit"] === "fetch") {
    $jira = new Jira();
    $period = $_POST['period'];
    $result = $jira->getData($period, $_POST['startdate'], $_POST['enddate']);
    $rows = $jira->buildRowFromData($result);
}
?>
<div class="container">
<?php if (!empty($rows)) { ?>
    <div class="row">
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
                    <td>Total:</td>
                    <td><?php echo round($total_minutes, 2); ?></td>
                    <td><?php echo round($total_minutes / 60, 2); ?></td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <p>There is no data available for the selected period.</p>
        </div>
    </div>

    <?php
} ?>

</div>
<?php
include_once "includes/footer.php";
