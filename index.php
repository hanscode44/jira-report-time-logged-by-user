<?php

require 'settings.php';
require 'includes/global.php';

if (!empty($_POST)) {
    if ($_POST["submit"] === "fetch") {

        $jira = new Jira();
        $period = $_POST['period'];
        $result = $jira->getData($period,$_POST['startdate'],$_POST['enddate']);
        $rows = $jira->buildRowFromData($result);
    }
}
?>

    <div class="container">

        <?php if (!empty($error)) {
            foreach ($error as $errorItem) {
                ?>
                <div class="row alert alert-danger">
                    <p><?php echo $errorItem ?></p>
                </div>
            <?php } ?>
        <?php } ?>


        <form method="POST" class="form-inline">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-10">

                        <div class="form-group">
                            <label><span>Period:</span>
                                <select name="period" class="form-control" id="periodSelector">
                                    <option value="today" <?php echo $_POST['period'] == 'today' ? 'selected' : ''; ?>>
                                        Today
                                    </option>
                                    <option value="yesterday" <?php echo $_POST['period'] == 'yesterday' ? 'selected' :
                                        ''; ?>>
                                        Yesterday
                                    </option>
                                    <option value="week" <?php echo $_POST['period'] == 'week' ? 'selected' : ''; ?>>
                                        Current
                                        week
                                    </option>
                                    <option value="period" <?php echo $_POST['period'] == 'period' ? 'selected' :
                                        ''; ?>>Select period
                                    </option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-success" value="fetch" name="submit">Run report</button>
                    </div>
                </div>
            </div>
            <div class="row hidden" id="datepickers">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Startdate:</label>
                        <input type="text" class="datepicker form-control" name="startdate" value="<?php echo isset($_POST['startdate']) ? $_POST['startdate'] :
                            ''; ?>">

                        <label>Enddate:</label>
                        <input type="text" class="datepicker form-control" name="enddate" value="<?php echo isset($_POST['enddate']) ? $_POST['enddate'] :
                            ''; ?>">
                    </div>

                </div>
            </div>
        </form>
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
                    <td>Total:</td>
                    <td><?php echo round($total_minutes, 2); ?></td>
                    <td><?php echo round($total_minutes / 60, 2); ?></td>
                </tr>

                </tbody>
            </table>
        </div>

    </div>
<?php } ?>

    </div>

<?php
include_once "includes/footer.php";
