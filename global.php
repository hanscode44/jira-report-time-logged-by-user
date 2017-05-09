<?php

function arrayPrint($array)
{
    echo "<pre>";
    var_dump($array);
    die;
}

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
    } else {
        $startDate = new \DateTime('2017-05-01');
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
        "and+timespent+%3E+0&fields=key,summary&maxResults=10"
    );

    $issues = json_decode(curl_exec($curl), true);
    foreach ($issues['issues'] as $issue) {

        $key = $issue['key'];
        $title = $issue['fields']['summary']; // needed for future addition

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

        $timespent = 0;

        foreach ($issue['timespent'] as $d => $ts) {
            foreach ($ts as $entry) {
                $timespent = $timespent + $entry;
                $arr[$i]['entry'][$d][]['minutes'] = $entry;
            }
        }

        $arr[$i]['total_time_spent_minutes'] = $timespent;
        $arr[$i]['total_time_spent_hours'] = $timespent / 60;
    }

    return $arr;
}
