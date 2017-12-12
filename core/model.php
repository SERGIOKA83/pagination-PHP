<?php
class Model{
    private $paragraphs;
    private $page;
    private $lastPage;
    private $dataLength = 0;
    private $numberOfData;
    private $action;
    private $messages;

    function __construct(string $action)
    {

        $this->action = $action;

    }
    private function doAction(): void
    {
        if(($this->action != 'read') && ($this->action != 'create'))
        {
           $this->action = 'read';
        }

        $runAction = $this->action;

        $runAction .= "Data";

        $this->$runAction();

    }

    function checkParagraphNumbers(int $paragraps): int
    {

        $maxParagraph = $this->calculateMaxParagraphs();

        if($paragraps <= FIRST_PARAGRAPHS_COUNT)
            $paragraps = FIRST_PARAGRAPHS_COUNT;
        else
        {
            if($paragraps > $maxParagraph)
                $paragraps = $maxParagraph;
            else
                for ($i = FIRST_PARAGRAPHS_COUNT; $i < $maxParagraph; $i *= 2)
                {

                    if($paragraps > $i && $paragraps <= $i*2)
                        $paragraps = $i*2;
                }
        }
        $this->paragraphs = $paragraps;
        return $paragraps;

    }

    function getPageData(int $page): array
    {
        $this->page = $page;

        $this->dataLength = $this->calcDataLength();

        $this->countLastPage();

        if (!$this->checkPageNumber())
            die('Страница ненайдена!');

        $this->doAction();

        return $this->messages;

    }

    private function countLastPage(): void
    {

        $this->numberOfData = filesize( FILE_NAME ) / $this->dataLength;

        if($this->numberOfData>0)
            $this->lastPage = ceil($this->numberOfData / $this->paragraphs );
        else
            $this->lastPage = 1;

    }

    private function checkPageNumber(): bool
    {

        return ($this->page >= 1 && $this->page <= $this->lastPage);

    }

    private function createData(): void
    {

        if ($fileHandle = fopen(FILE_NAME,'a'))
        {

            $name = $this->checkString($_POST['name'], NAME);

            fwrite($fileHandle, $name,NAME);

            $message = $this->checkString($_POST['message'], MESSAGE);

            fwrite($fileHandle, $message, MESSAGE);

            $time = date('G:i:s, d.m.Y');

            $time = $this->checkString($time, DATE);

            fwrite($fileHandle,$time, DATE);

            fclose($fileHandle);
        }

        header("Location: {$_SERVER['SCRIPT_NAME']}");

        exit;

    }

    private function checkString(string $data, int $length): string
    {
        $data = strip_tags($data);

        $lenData =  mb_strlen($data,"UTF-8");

        if($lenData < $length)
            $data = str_pad($data, $length);

        return $data;
    }

    private function readData(): void
    {

        $allDataOnPage = $this->paragraphs * $this->dataLength;

        $firstDataOnPage = $this->page * $allDataOnPage;

        if (filesize( FILE_NAME ))
        {
            if ($fileHandle = fopen(FILE_NAME, 'r'))
            {

                if (fseek($fileHandle, (-1 * $firstDataOnPage), SEEK_END))
                    $limit = $this->numberOfData % $this->paragraphs;
                else
                    $limit = $this->paragraphs;

                for ($i = 0; $i < $limit; $i++)
                {

                    $messagesArray[$i] = ['name' => trim(fread($fileHandle, NAME)),
                                          'message' => trim(fread($fileHandle, MESSAGE)),
                                          'time' => trim(fread($fileHandle, DATE))];
                }

                fclose($fileHandle);

             }
        }
        else
        {
            $messagesArray[1] = ['name' => 'пусто', 'message' => 'пусто', 'time' => 'пусто'];
        }

        $this->messages = array_reverse($messagesArray);

    }

    function getLastPage(): int
    {

        return $this->lastPage;

    }

    private function calculateMaxParagraphs(): int
    {

        return FIRST_PARAGRAPHS_COUNT * pow(2,(QANTITY_PARAGRAPH_NUMBERS-1));

    }

    private function calcDataLength(): int
    {

        return NAME + MESSAGE + DATE;

    }

}