<?php
namespace JiraReport;


class Data
{
    /**
     * @var \DateTime
     */
    protected $date;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var string
     */
    protected $summary;
    /**
     * @var int
     */
    protected $timeSpent;

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * @param int $timeSpent
     * @return $this
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;
        return $this;
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return \explode('-', $this->getKey(), 2)[0];
    }
}
