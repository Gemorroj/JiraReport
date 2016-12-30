<?php
namespace JiraReport;


abstract class Filter
{
    protected $jsonData;

    /**
     * Filter constructor.
     * @param \stdClass $jsonData
     */
    public function setJsonData(\stdClass &$jsonData)
    {
        $this->jsonData = $jsonData;
    }


    abstract public function run();
}
