<?php
require 'settings.php';
require 'includes/global.php';

if (!empty($_POST) && $_POST["submit"] === "fetch") {

    $jira = new Jira();
    $period = $_POST['period'];
    $username = strlen($_POST['naam']) > 0 ? $_POST['naam'] : $cfg['jira_user_name'];
    $result = $jira->getData($period, $username, $_POST['startdate'], $_POST['enddate']);
    $rows = $jira->buildRowFromData($result);
    ?>

    <div class="container">
        <div class="results">
            <?php if (!empty($rows)) { ?>

                <div class="row">
                    <h1>Time spent <?php echo $period; ?></h1>
                </div>

                <div class="row">
                    <div class="col-sm-8">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="150">Ticket#</th>
                                    <th>Prio</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th width="70"><i class="fa fa-clock-o"></i> (min.)</th>
                                    <th width="70"><i class="fa fa-clock-o"></i> (hrs.)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_minutes = 0;
                                $total_hours = 0;
                                $totalTicketMinutes = 0;

                                foreach ($rows as $index => $row) {
                                    $totalTicketMinutes = $totalTicketMinutes + $row['total_ticket_time'];
                                    ?>

                                    <tr class="ticket">
                                        <td><a href="<?php echo $cfg['jira_host_address']; ?>/browse/<?php echo $index; ?>"
                                               target="_blank"><?php echo $index; ?> <i class="fa fa-external-link"></i>
                                            </a></td>
                                        <td><img src="<?php echo $row['priorityImage']; ?>" height="18"
                                                 title="<?php echo $row['priority']; ?>"></td>
                                        <td><?php echo $row['description']; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td><?php echo $row['total_ticket_time']; ?></td>
                                        <td><?php echo round($row['total_ticket_time'] / 60, 2); ?></td>
                                        <td id="<?php echo $index; ?>">
                                            <div class="entrySummary"><i class="fa fa-plus-square"></i></div>
                                            <div class="entrySummaryHide hidden"><i class="fa fa-minus-square"></i></div>
                                        </td>
                                    </tr>

                                    <?php
                                    foreach ($row['entry'] as $date => $entry) {
                                        $newDate = new \DateTime($date);
                                        $rowDate = $newDate->format('d-m-Y');
                                        $entryDaySummaryClass = $index . "-" . $rowDate . "-day";
                                        $entryDayDetailClass = $index . "-" . $rowDate;
                                        ?>

                                        <tr class="hidden" data-ticket="<?php echo $index; ?>" data-type="summary" data-date="<?php echo $rowDate; ?>">
                                            <td></td>
                                            <td colspan="3"><?php echo $rowDate; ?></td>
                                            <td><?php echo $entry['total_time']; ?></td>
                                            <td><?php echo round($entry['total_time'] / 60, 2); ?></td>
                                            <td>
                                                <div class="entryDetail"><i class="fa fa-plus-square"></i></div>
                                                <div class="entryDetailHide hidden"><i class="fa fa-minus-square"></i></div>
                                            </td>
                                        </tr>

                                        <?php
                                        foreach ($entry as $time) {
                                            foreach ($time as $rule) {
                                                ?>
                                                <tr class="hidden" data-ticket="<?php echo $index; ?>" data-type="detail" data-date="<?php echo $rowDate; ?>">
                                                    <td colspan="2"></td>
                                                    <td colspan="2"><?php echo $rule['spent_time']['description']; ?></td>
                                                    <td><?php echo $rule['spent_time']['minutes']; ?></td>
                                                    <td><?php echo round($rule['spent_time']['minutes'] / 60, 2); ?></td>
                                                    <td></td>
                                                </tr>

                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>

                                <tr class="total">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Total:</td>
                                    <td><?php echo round($totalTicketMinutes, 2); ?></td>
                                    <td><?php echo round($totalTicketMinutes / 60, 2); ?></td>
                                    <td></td>
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
            }
            ?>

        </div>
    </div>
    <?php
} else {
    ?>
    <div class="container">
        <div class="results">
            <div class="row">
                <div class="col-lg-12">
                    <p>Please select an period and press submit to get an overview of worked time.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
include_once "includes/footer.php";
