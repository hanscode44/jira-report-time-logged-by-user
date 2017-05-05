<?php

/**
 * Update these configuration variables to  match your own JIRA settings.
 *
 * Refer to README.md for more information.
 */
$cfg = [
    'jira_host_address' => 'https://atlassian.hotflo.net/jira',
    'jira_user_email' => 'looshan',
    'jira_user_password' => 'CiP@/993c',
    'max_results' => '500'
];

/**
 * Local Composer
 */
require 'vendor/autoload.php';

session_start();
$error = "";

function getData($key, $period)
{

    global $cfg;
    global $error;

    if ($period == 'vandaag') {
        $startDate = new \DateTime();
        $endDate = new \DateTime('tomorrow');
    } else if ($period == 'gisteren') {
        $startDate = new \DateTime('yesterday');
        $endDate = new \DateTime();
    } else if ($period == 'week') {
        $startDate = new \DateTime('monday this week');
        $endDate = new \DateTime('saturday this week');
    }

    $fromDate = $startDate->format('Y-m-d');
    $toDate = $endDate->format('Y-m-d');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERPWD, $cfg['jira_user_email'] . ':' . $cfg['jira_user_password']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    # Give me up to 1000 search results with the Key, where
    # assignee = $assignee  AND  project = $project
    #  AND created < $toDate  AND  updated > $fromDate
    #  AND timespent > 0
    curl_setopt(
        $curl,
        CURLOPT_URL,
        $cfg['jira_host_address'] . "/rest/api/2/search?startIndex=0&jql=" .
        "worklogAuthor=looshan+and+updated+%3E+$fromDate+" .
        "and+timespent+%3E+0&fields=key&maxResults=10"
    );

    $issues = json_decode(curl_exec($curl), true);
    foreach ($issues['issues'] as $issue) {

        $key = $issue['key'];
        # for each issue in result, give me the full worklog for that issue
        curl_setopt(
            $curl,
            CURLOPT_URL,
            $cfg['jira_host_address'] . "/rest/api/2/issue/$key/worklog"
        );

        $worklog = json_decode(curl_exec($curl), true);

        foreach ($worklog['worklogs'] as $entry) {
            if ($entry['author']['name'] == $cfg['jira_user_email']) {
                $shortDate = substr($entry['started'], 0, 10);


                $startDate = new \DateTime($entry['started']);

                # keep a worklog entry on $key item,
                # iff within the search time period
                if ($shortDate >= $fromDate && $shortDate < $toDate) {
                    $periodLog[$key]['timespent'][$startDate->format('Y-m-d')][] = $entry['timeSpentSeconds'] / 60;
                }
            }
        }
    }

    return $periodLog;

}

function buildRowFromData($data)
{
    global $error;

    if (empty($data)) {
        $error = 'Error: Request did not return any results, check login information or project key';

        return false;
    }

    $arr = [];

    foreach ($data as $i => $issue) {

        $field = $issue['fields'];

        $arr[$i]['key'] = $i;
//        $arr[$i]['assignee'] = $field['assignee']['displayName'];
//        $arr[$i]['status'] = $field['status']['name'];
//        $arr[$i]['priority'] = $field['priority']['name'];
//        $arr[$i]['summary'] = $field['summary'];
//        $arr[$i]['time_estimate'] = $field['timeestimate'];

        $timespent = 0;
        foreach ($issue['timespent'] as $ts) {
            $timespent = $timespent + $ts;
        }

        $arr[$i]['total_time_spent_minutes'] = $timespent;
        $arr[$i]['total_time_spent_hours'] = $timespent / 60;
    }

    return $arr;
}


if (!empty($_POST)) :
    if ($_POST["submit"] === "fetch") {

        $jiraKey = strtoupper($_POST["jira_key"]);
        $period = $_POST['period'];
        $result = getData($jiraKey, $period);

        $decodedData = json_decode($result, true);
        $rows = buildRowFromData($result);

        $_SESSION['export'] = $rows;

    }
endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JIRA Export</title>
    <link rel="stylesheet" href="https://unpkg.com/purecss@0.6.2/build/pure-min.css"
          integrity="sha384-UQiGfs9ICog+LwheBSRCt1o5cbyKIHbwjWscjemyBMT9YCUMZffs6UqUTd0hObXD" crossorigin="anonymous">
    <style>body {
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
<?php if (!empty($error)) : ?>
    <div>
        <p><?php echo $error ?></p>
    </div>
<?php endif; ?>

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


        <!--        <label>Enter JIRA Project Key<input type="text" name="jira_key" placeholder="Eg. PROJ, ABC"></label>-->


        <button name="submit" class="button-success button-xlarge pure-button" value="fetch">Run Report</button>
    </div>
</form>
<?php if (!empty($rows)) : ?>
    <hr/>
    <!--    <h3>Results-->
    <!--        <button name="submit" class="button-secondary button-small pure-button" value="export">Export CSV</button>-->
    <!--    </h3>-->
    <table class="pure-table pure-table-bordered">
    <thead>
    <tr>
        <th width="150">Key</th>
        <th>Assignee</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Summary</th>
        <th>Time Estimated</th>
        <th>Total Time Spent (min.)</th>
        <th>Total Time Spent (hrs.)</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $total_minutes = 0;
    $total_hours = 0;

    foreach ($rows as $index => $row) : ?>

        <?php
        $total_minutes = $total_minutes + $row['total_time_spent_minutes'];
        $total_hours = $total_hours + $row['total_time_spent_hours'];
        ?>

        <tr>
            <td><a href="https://atlassian.hotflo.net/jira/browse/<?php echo $row['key']; ?>"
                   target="_blank"><?php echo $row['key']; ?></a></td>
            <td><?php echo $row['assignee']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['priority']; ?></td>
            <td><?php echo $row['summary']; ?></td>
            <td><?php echo $row['time_estimate']; ?></td>
            <td><?php echo round($row['total_time_spent_minutes'], 2); ?></td>
            <td><?php echo round($row['total_time_spent_hours'], 2); ?></td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>Totaal</td>
        <td><?php echo round($total_minutes, 2); ?></td>
        <td><?php echo round($total_hours, 2); ?></td>
    </tr>

    </tbody>
    </table><?php
endif ?>

</body>
</html>