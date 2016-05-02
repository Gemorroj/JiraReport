<?php
namespace Gemorroj\JiraReport;

class Jira
{
    const BASE_API_URL = 'https://support.softclub.by/rest/api/latest';
    const BASE_BROWSE_URL = 'https://support.softclub.by/browse';

    /**
     * @var JiraIssue[]
     */
    protected $data;
    /**
     * @var \stdClass[]
     */
    protected $issues;
    /**
     * @var resource
     */
    private $stream;
    /**
     * @var \stdClass
     */
    protected $jsonData;
    /**
     * @var string
     */
    protected static $username;
    /**
     * @var \DateTime
     */
    protected static $worklogDateFrom;
    /**
     * @var \DateTime
     */
    protected static $worklogDateTo;

    /**
     * Jira constructor.
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        self::$username = $username;

        $this->stream = \stream_context_create(
            array('http' =>
                array(
                    'method' => 'GET',
                    'header' => "Authorization: Basic " . \base64_encode("$username:$password") . "\r\n",
                )
            )
        );
    }


    /**
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function setWorklogDates(\DateTime $from, \DateTime $to)
    {
        self::$worklogDateFrom = $from;
        self::$worklogDateTo = $to;
    }


    /**
     * @return \DateTime
     */
    public static function getWorklogDateFrom()
    {
        return self::$worklogDateFrom;
    }

    /**
     * @return \DateTime
     */
    public static function getWorklogDateTo()
    {
        return self::$worklogDateTo;
    }
    
    
    /**
     * @return string
     */
    public static function getUsername()
    {
        return self::$username;
    }


    /**
     * @param string $jql
     * @return array
     * @throws \Exception
     */
    public function findIssues($jql)
    {
        $url = Jira::BASE_API_URL . '/search?maxResults=1000&fields=*all&jql=' . \rawurlencode($jql);

        $rawData = \file_get_contents($url, null, $this->stream);
        if (false === $rawData) {
            throw new \Exception('Не удалось получить данные (' . $url . ').');
        }

        $this->jsonData = json_decode($rawData);
        if (!$this->jsonData) {
            throw new \Exception('Не удалось преобразовать данные (' . $url . ').');
        }

        if ($this->jsonData->errorMessages) {
            throw new \Exception(\implode("\n", $this->jsonData->errorMessages));
        }

        foreach ($this->jsonData->issues as $issue) {
            $this->issues[] = $issue;
        }

        return $this->issues;
    }


    /**
     *
     */
    public function makeData()
    {
        foreach ($this->issues as $issue) {
            $this->data[] = new JiraIssue($issue);
        }
    }


    /**
     * @return JiraIssue[]
     */
    public function getData()
    {
        return $this->data;
    }
}
