<?php
namespace JiraReport;

class JiraIssue
{
    /**
     * @var \stdClass
     */
    protected $jsonData;

    /**
     * @param \stdClass $issue
     * @throws \Exception
     */
    public function __construct(\stdClass $issue)
    {
        $this->jsonData = $issue;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->jsonData->fields->worklog->worklogs) {
            return $this->makeDataWorklog();
        } else {
            return $this->makeDataResolution();
        }
    }


    /**
     * @param array $data
     * @return array
     */
    private function makeDataResolution()
    {
        $data = array();
        $data[] = array(
            'date' => new \DateTime($this->jsonData->fields->resolutiondate),
            'key' => $this->jsonData->key,
            'summary' => $this->jsonData->fields->summary,
            'timeSpent' => $this->jsonData->fields->timespent,
        );
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function makeDataWorklog()
    {
        $data = array();
        foreach ($this->jsonData->fields->worklog->worklogs as $worklog) {
            if ($worklog->author->key !== Jira::getUsername()) {
                continue;
            }

            $date = new \DateTime($worklog->created);
            if ((Jira::getWorklogDateFrom() && $date < Jira::getWorklogDateFrom()) || (Jira::getWorklogDateTo() && $date > Jira::getWorklogDateTo())) {
                continue;
            }

            $data[] = array(
                'date' => $date,
                'key' => $this->jsonData->key,
                'summary' => $this->jsonData->fields->summary,
                'timeSpent' => $worklog->timeSpentSeconds,
            );
        }
        return $data;
    }
}
