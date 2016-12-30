<?php
namespace JiraReport;

use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\XLSX\Reader;
use Box\Spout\Reader\XLSX\Sheet;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\XLSX\Writer;

class Excel
{
    /**
     * @var string
     */
    protected $outputFilePath;
    protected $inputFilePath;

    /**
     * Excel constructor.
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->outputFilePath = $filePath;
        $this->inputFilePath = __DIR__ . '/../template.xlsx';
    }


    /**
     * @param Jira $jira
     * @throws \Exception
     */
    public function makeExcel(Jira $jira)
    {
        /** @var Reader $reader */
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($this->inputFilePath);

        /** @var Writer $writer */
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($this->outputFilePath);

        $this->copyTemplate($reader, $writer);
        $this->addData($jira, $writer);

        $reader->close();
        $writer->close();
    }

    /**
     * @param Reader $reader
     * @param Writer $writer
     * @throws \Exception
     */
    private function copyTemplate(Reader $reader, Writer $writer)
    {
        /** @var Sheet $sheet */
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            if ($sheetIndex !== 1) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            foreach ($sheet->getRowIterator() as $row) {
                $writer->addRow($row);
            }
        }
    }

    /**
     * @param Jira $jira
     * @param Writer $writer
     * @throws \Exception
     */
    protected function addData(Jira $jira, Writer $writer)
    {
        $totalSpent = 0;
        $projectsSpent = array();

        foreach ($jira->getData() as $jiraIssue) {
            foreach ($jiraIssue->getData() as $data) {

                $totalSpent += $data->getTimeSpent();
                $projectsSpent[$data->getProjectName()] += $data->getTimeSpent();

                $writer->addRow(array(
                    $data->getDate()->format('d.m.Y'),
                    $data->getKey(),
                    $data->getSummary(),
                    $data->getTimeSpent() ? $this->formatTimeSpent($data->getTimeSpent()) : '',
                ));
            }
        }

        $writer->addRow(array(''));
        $writer->addRow(array('Итого:', $this->formatTimeSpent($totalSpent) . ' ч.'));

        if (count($projectsSpent) > 1) {
            foreach ($projectsSpent as $projectName => $projectSpent) {
                $writer->addRow(array($projectName, $this->formatTimeSpent($projectSpent) . ' ч.'));
            }
        }
    }

    /**
     * @param float $timeSpent
     * @return float
     */
    protected function formatTimeSpent($timeSpent)
    {
        return \round($timeSpent / 3600, 1);
    }
}
