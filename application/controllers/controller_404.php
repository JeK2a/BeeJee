<?php

declare(strict_types=1);

class Controller_404 extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function action_index(): void
    {
        http_response_code(404);
        $this->view->generate('404_view.php', 'template_view.php');
    }
}
