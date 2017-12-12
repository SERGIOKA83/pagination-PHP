<?php
class View{
    private $pageData;
    private $paragraphs;
    private $page;
    private $lastPage;

    function __construct(int $lastPage, int $paragraphs)
    {
        $this->lastPage = $lastPage;
        $this->paragraphs = $paragraphs;
    }
    function render(array $pageData, int $page)
    {
        $this->pageData = $pageData;

        $this->page = $page;

        $this->renderPageNumbers();

        $this->renderData();

        $this->renderPagination();
        $this->renderForm();
    }

    private function renderPageNumbers(): void
    {

        $maxParagraph = $this->calculateMaxParagraphs();

        echo 'Колличество абзацев: ';

        for ($i = FIRST_PARAGRAPHS_COUNT; $i <= $maxParagraph; $i *= 2)
        {
            if ($i!=$this->paragraphs)
                echo "<a href=\"{$_SERVER['SCRIPT_NAME']}?parag=$i\"> $i&nbsp; </a> ";
            else
                echo ' ',$i, ' ';

            if ($i < $maxParagraph)
                echo '|';
            else
                echo '</br>';
        }

    }

    private function renderData(): void
    {

        foreach($this->pageData as $messages)
        {

            echo "<p>Имя: {$messages['name']}<br>
                     Сообщение: {$messages['message']}<br>
                     Время: {$messages['time']}</p>";

        }

    }

    private function renderForm(): void
    {

       echo '<form method="POST" action="index.php">
                <p>Введите имя:<br>
                    <input type="text" name="name"></p>
                <p>Введите сообщение:<br>
                    <textarea name="message" ></textarea></p>
                <input type="submit" value="Отправить">
                <input type="hidden" name="action" value="create">
            </form>';

    }

    private function renderPagination(): void
    {

        for($i = 1; $i <= $this->lastPage; $i++)
            if($i != $this->page)
                echo "<a href=\"{$_SERVER['SCRIPT_NAME']}?page=$i&parag=$this->paragraphs\"> $i&nbsp; </a>";
            else
                echo " $i ";

    }

    private function calculateMaxParagraphs(): int
    {

        return FIRST_PARAGRAPHS_COUNT * pow(2,(QANTITY_PARAGRAPH_NUMBERS-1));

    }

}