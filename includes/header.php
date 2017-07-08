<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JIRA Time overview</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../src/jquery-ui/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../src/bootstrap/dist/css/bootstrap.css">
    <!-- Optional Bootstrap theme -->
    <link rel="stylesheet" href="../src/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../src/fortawesome/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

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
                                <option value="today" <?php echo isset($_POST['period']) && $_POST['period'] == 'today' ? 'selected' : ''; ?>>
                                    Today
                                </option>
                                <option value="yesterday" <?php echo isset($_POST['period']) && $_POST['period'] == 'yesterday' ? 'selected' :
                                    ''; ?>>
                                    Yesterday
                                </option>
                                <option value="week" <?php echo isset($_POST['period']) && $_POST['period'] == 'week' ? 'selected' : ''; ?>>
                                    Current
                                    week
                                </option>
                                <option value="period" <?php echo isset($_POST['period']) && $_POST['period'] == 'period' ? 'selected' :
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
    <hr/>
</div>

