<?php
namespace JiraReport;

class JiraIssue
{
    /**
     * @var \stdClass
     */
    protected $jsonData;
    /**
     * @var Filter|null
     */
    protected $filter;

    /**
     * @param \stdClass $issue
     * @param Filter|null $filter
     * @throws \Exception
     */
    public function __construct(\stdClass $issue, Filter $filter = null)
    {
        $this->jsonData = $issue;
        $this->filter = $filter;
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

            if (!$this->applyFilter($worklog)) {
                continue;
            }

            $data[] = (new Data())
                ->setDate(new \DateTime($worklog->created))
                ->setKey($this->jsonData->key)
                ->setSummary($this->jsonData->fields->summary)
                ->setTimeSpent($worklog->timeSpentSeconds);
        }

        return $data;
    }


    /**
     * @param \stdClass $worklog
     * @return bool
     */
    protected function applyFilter(\stdClass $worklog)
    {
        if (!$this->filter) {
            return true;
        }

        $worklogDate = new \DateTime($worklog->created);

        if (null !== $this->filter->getUsername() && $worklog->author->key !== $this->filter->getUsername()) {
            return false;
        }

        if (null !== $this->filter->getWorklogDateFrom() && $worklogDate < $this->filter->getWorklogDateFrom()) {
            return false;
        }

        if (null !== $this->filter->getWorklogDateTo() && $worklogDate > $this->filter->getWorklogDateTo()) {
            return false;
        }

        return true;
    }
}
