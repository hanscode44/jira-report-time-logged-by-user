<?php

use Curl\Curl;

/**
 * Class jira
 */
class jira
{

    /**
     * @param $period string
     *
     * @return mixed
     */
    function getData($period)
    {
        global $cfg;
        global $error;

        if ($period == 'today') {
            $startDate = new \DateTime();
            $endDate = new \DateTime('tomorrow');
        } else if ($period == 'yesterday') {
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
        $periodLog = [];

        $curl = new Curl();
        $curl->setOpt(CURLOPT_USERPWD, $cfg['jira_user_name'] . ':' . $cfg['jira_user_password']);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);

        $curl->setOpt(
            CURLOPT_URL,
            $cfg['jira_host_address'] . "/rest/api/2/search?startIndex=0&jql=" .
            "worklogAuthor=" . $cfg['jira_user_name'] . "+and+updated+%3E+$fromDate+" .
            "and+timespent+%3E+0&fields=key,summary&maxResults=100"
        );

        $curl->exec();
        $returnData = json_decode(json_encode($curl->response), true);

        foreach ($returnData['issues'] as $issue) {

            $key = $issue['key'];
            $title = $issue['fields']['summary']; // needed for future addition

            $curl->setOpt(CURLOPT_URL, $cfg['jira_host_address'] . "/rest/api/2/issue/$key/worklog");
            $curl->exec();

            $worklog = json_decode(json_encode($curl->response), true);

            foreach ($worklog['worklogs'] as $entry) {
                if ($entry['author']['name'] == $cfg['jira_user_name']) {
                    $shortDate = substr($entry['started'], 0, 10);
                    $startDate = new \DateTime($entry['started']);

                    if ($shortDate >= $fromDate && $shortDate < $toDate) {
                        $periodLog[$key]['timespent'][$startDate->format('Y-m-d')][] = $entry['timeSpentSeconds'] / 60;
                    }
                }
            }
        }

        $curl->close();

        return $periodLog;

    }

    /**
     * @param $data array
     *
     * @return array|bool
     */
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
}
