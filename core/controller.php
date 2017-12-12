<?php
class Controller{
    private $paragraphs = 1;
    private $page = 1;
    private $action = 'read';
    private $pageData;
    private $lastPage;
    private $model;
    private $view;


    function run(): void
    {

        $this->getParam();

        $this->model = new Model($this->action);

        $this->paragraphs = $this->model->checkParagraphNumbers($this->paragraphs);

        $this->pageData = $this->model->getPageData($this->page);

        $this->lastPage = $this->model->getLastPage();

        // var_dump($this->pageData);

        $this->view = new View($this->lastPage, $this->paragraphs);

        $this->view->render($this->pageData, $this->page);

    }

    private function getParam(): void
    {

        if(isset($_POST['action'])) $this->action = $_POST['action'];

        if(isset($_GET['parag'])) $this->paragraphs = intval($_GET['parag']);

        if(isset($_GET['page'])) $this->page = intval($_GET['page']);

    }


}