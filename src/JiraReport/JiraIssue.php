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
     * @return Data[]
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
     * @return Data[]
     */
    private function makeDataResolution()
    {
        $data = array();

        $data[] = (new Data())
            ->setDate(new \DateTime($this->jsonData->fields->resolutiondate))
            ->setKey($this->jsonData->key)
            ->setSummary( $this->jsonData->fields->summary)
            ->setTimeSpent($this->jsonData->fields->timespent);

        return $data;
    }

    /**
     * @return Data[]
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

            $data[] = (new Data())
                ->setDate($date)
                ->setKey($this->jsonData->key)
                ->setSummary($this->jsonData->fields->summary)
                ->setTimeSpent($worklog->timeSpentSeconds);
        }

        return $data;
    }
}
