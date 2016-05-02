<?php
namespace JiraReport;

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\XLSX\Reader;
use Box\Spout\Reader\XLSX\Sheet;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
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
        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($this->inputFilePath);

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
    private function addData(Jira $jira, Writer $writer)
    {
        $totalSpent = 0;

        foreach ($jira->getData() as $jiraIssue) {
            foreach ($jiraIssue->getData() as $data) {

                $totalSpent += $data['timeSpent'];

                $writer->addRow(array(
                    $data['date']->format('d.m.Y'),
                    $data['key'],
                    $data['summary'],
                    $data['timeSpent'] ? $this->formatTimeSpent($data['timeSpent']) : '',
                ));

            }
        }

        $writer->addRow(array(''));
        $writer->addRow(array('Итого:', $this->formatTimeSpent($totalSpent) . ' ч.'));
    }

    /**
     * @param float $timeSpent
     * @return float
     */
    private function formatTimeSpent($timeSpent)
    {
        return \round($timeSpent / 3600, 1);
    }
}
