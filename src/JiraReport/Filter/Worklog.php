<?php
namespace JiraReport\Filter;


use JiraReport\Filter;

class Worklog extends Filter
{
    /**
     * @var string|null
     */
    private $author;
    /**
     * @var \DateTime|null
     */
    private $dateFrom;
    /**
     * @var \DateTime|null
     */
    private $dateTo;

    /**
     * @return null|string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param null|string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTime|null $dateFrom
     * @return $this
     */
    public function setDateFrom(\DateTime $dateFrom = null)
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param \DateTime|null $dateTo
     * @return $this
     */
    public function setDateTo(\DateTime $dateTo = null)
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    public function run()
    {
        foreach ($this->jsonData->fields->worklog->worklogs as &$worklog) {
            $worklogDate = new \DateTime($worklog->created);

            if (null !== $this->getAuthor() && $worklog->author->key !== $this->getAuthor()) {
                unset($worklog);
                return;
            }

            if (null !== $this->getDateFrom() && $worklogDate < $this->getDateFrom()) {
                unset($worklog);
                return;
            }

            if (null !== $this->getDateTo() && $worklogDate > $this->getDateTo()) {
                unset($worklog);
                return;
            }
        }
    }
}
