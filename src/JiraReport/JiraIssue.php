<?php
namespace JiraReport;

use JiraReport\Filter\Worklog;

class JiraIssue
{
    /**
     * @var \stdClass
     */
    protected $jsonData;
    /**
     * @var Filter[]
     */
    protected $filters;

    /**
     * @param \stdClass $issue
     * @param Filter[] $filters
     * @throws \Exception
     */
    public function __construct(\stdClass $issue, array $filters = array())
    {
        $this->jsonData = $issue;
        $this->filters = $filters;
    }

    /**
     * @return Data[]
     */
    public function getData()
    {
        $this->applyFilters($this->jsonData);

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
            $data[] = (new Data())
                ->setDate(new \DateTime($worklog->created))
                ->setKey($this->jsonData->key)
                ->setSummary($this->jsonData->fields->summary)
                ->setTimeSpent($worklog->timeSpentSeconds);
        }

        return $data;
    }


    /**
     * @param \stdClass $jsonData
     */
    protected function applyFilters(\stdClass &$jsonData)
    {
        foreach ($this->filters as $filter) {
            $filter->setJsonData($jsonData);
            $filter->run();
        }
    }
}
