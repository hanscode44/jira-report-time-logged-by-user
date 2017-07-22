<?php

require 'settings.php';
require 'includes/global.php';

if ( ! empty( $_POST ) && $_POST["submit"] === "fetch" ) {
	$jira   = new Jira();
	$period = $_POST['period'];
	$result = $jira->getData( $period, $_POST['startdate'], $_POST['enddate'] );
	$rows   = $jira->buildRowFromData( $result );

	?>

    <div class="container">
        <div class="results">
		<?php if ( ! empty( $rows ) ) { ?>

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
                            <th width="150">Date</th>
                            <th width="150"><i class="fa fa-clock-o"></i> (min.)</th>
                            <th width="150"><i class="fa fa-clock-o"></i> (hrs.)</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php

						$total_minutes = 0;
						$total_hours   = 0;

						$totalTicketMinutes = 0;

						foreach ( $rows as $index => $row ) {

							$minutes = 0;
							$teller  = 0;
							?>
                            <tr class="ticket">

                                <td><a href="<?php echo $cfg['jira_host_address']; ?>/browse/<?php echo $index; ?>"
                                   target="_blank"><?php echo $index; ?> <i class="fa fa-external-link"></i> </a></td>
                                <td><img src="<?php echo $row['priorityImage'];?>" height="18" title="<?php echo $row['priority'];?>"></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['status']; ?></td>

							<?php
							foreach ( $row['entry'] as $date => $entry ) {

								$newDate = new \DateTime( $date );
								$rowDate = $newDate->format( 'd-m-Y' );

								if ( $teller != 0 ) {
									?>
                                    <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
									<?php
								}
								?>

                                <td><?php echo $rowDate; ?></td>

								<?php
								$entryMinutes = 0;
								$entryDetail  = '';
								foreach ( $entry as $time ) {
									$entryMinutes     = $entryMinutes + $time['minutes'];
									$entryDetailClass = $index . "-" . $rowDate;
									$entryDetail .= "<tr class='hidden " .
									                $entryDetailClass .
									                "'><td></td><td colspan='4'>" .
									                $time['description'] .
									                "</td><td>" .
									                $time['minutes'] .
									                "</td><td>" .
									                round( $time['minutes'] / 60, 2 ) .
									                "</td><td></td></tr>";
								}
								?>

                                <td><?php echo $entryMinutes; ?></td>
                                <td><?php echo round( $entryMinutes / 60, 2 ); ?></td>
                                <td id="<?php echo $index; ?>-<?php echo $rowDate; ?>">
                                    <div class="entryDetail"><i
                                                class="fa fa-plus-square"></i></div>
                                    <div class="entryDetailHide hidden"><i
                                                class="fa fa-minus-square"></i></div>
                                </td>
                                </tr>


								<?php
								echo $entryDetail;
								$total_minutes = $total_minutes + $entryMinutes;
								$teller ++;
							}
							?>
							<?php
						}
						?>

                        <tr class="total">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total:</td>
                            <td><?php echo round( $total_minutes, 2 ); ?></td>
                            <td><?php echo round( $total_minutes / 60, 2 ); ?></td>
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
		} ?>
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
