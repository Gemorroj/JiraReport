<?php
namespace JiraReport;

class JiraIssue
{
    /**
     * @var \stdClass
     */
    protected $jsonData;
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param \stdClass $issue
     * @param Filter $filter
     * @throws \Exception
     */
    public function __construct(\stdClass $issue, Filter $filter)
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
            if (null !== $this->filter->getUsername() && $worklog->author->key !== $this->filter->getUsername()) {
                continue;
            }

            $date = new \DateTime($worklog->created);
            if (($this->filter->getWorklogDateFrom() && $date < $this->filter->getWorklogDateFrom()) || ($this->filter->getWorklogDateTo() && $date > $this->filter->getWorklogDateTo())) {
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
