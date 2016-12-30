<?php
namespace JiraReport;

class Jira
{
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
    private $httpStream;
    /**
     * @var string
     */
    protected $domain;
    /**
     * @var Filter[]
     */
    protected $filters = array();

    /**
     * Jira constructor.
     * @param string $username
     * @param string $password
     * @param string $domain
     */
    public function __construct($username, $password, $domain = 'https://support.softclub.by')
    {
        $this->domain = $domain;

        $this->httpStream = \stream_context_create(
            array('http' =>
                array(
                    'method' => 'GET',
                    'header' => "Authorization: Basic " . \base64_encode("$username:$password") . "\r\n",
                )
            )
        );
    }


    /**
     * @param string $jql
     * @return array
     * @throws \Exception
     */
    public function findIssues($jql)
    {
        $url = $this->domain . '/rest/api/latest/search?maxResults=1000&fields=*all&jql=' . \rawurlencode($jql);

        $rawData = \file_get_contents($url, null, $this->httpStream);
        if (false === $rawData) {
            throw new \Exception('Не удалось получить данные (' . $url . ').');
        }

        $jsonData = json_decode($rawData);
        if (!$jsonData) {
            throw new \Exception('Не удалось преобразовать данные (' . $url . ').');
        }

        if ($jsonData->errorMessages) {
            throw new \Exception(\implode("\n", $jsonData->errorMessages));
        }

        foreach ($jsonData->issues as $issue) {
            $this->issues[] = $issue;
        }

        return $this->issues;
    }

    /**
     * @param Filter $filter
     * @return $this
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Make data
     */
    public function makeData()
    {
        foreach ($this->issues as $issue) {
            $this->data[] = new JiraIssue($issue, $this->filters);
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
