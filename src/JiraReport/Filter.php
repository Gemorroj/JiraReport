<?php
namespace JiraReport;


class Filter
{
    /**
     * @var string|null
     */
    private $username;
    /**
     * @var \DateTime|null
     */
    private $worklogDateFrom;
    /**
     * @var \DateTime|null
     */
    private $worklogDateTo;

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     * @return Filter
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getWorklogDateFrom()
    {
        return $this->worklogDateFrom;
    }

    /**
     * @param \DateTime|null $worklogDateFrom
     * @return Filter
     */
    public function setWorklogDateFrom(\DateTime $worklogDateFrom = null)
    {
        $this->worklogDateFrom = $worklogDateFrom;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getWorklogDateTo()
    {
        return $this->worklogDateTo;
    }

    /**
     * @param \DateTime|null $worklogDateTo
     * @return Filter
     */
    public function setWorklogDateTo(\DateTime $worklogDateTo = null)
    {
        $this->worklogDateTo = $worklogDateTo;
        return $this;
    }
}
