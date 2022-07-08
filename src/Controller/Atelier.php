<?php

namespace Atelier\Controller;

use Atelier\Directory;
use Atelier\Flash;
use League\Plates\Engine;

class Atelier
{
    private Engine $templatesEngine;

    public function __construct()
    {
        $this->templatesEngine = new Engine(Directory::getTemplatesDirectory() . '/atelier');
        $this->templatesEngine->addData([
            'flash' => Flash::receive(),
        ]);
    }

    public function showIndex()
    {
        $this->redirect('/garderobe');
    }

    public function showGarderobe()
    {
    //        $this->templatesEngine->addData([
//            'title' => $this->replaceHtml($page->getTitle()),
//            'description' => $this->replaceHtml($page->getDescription()),
//            'h1' => $this->replaceHtml($page->getH1()),
//            'regions' => !is_numeric($limit) || intval($limit) > 0
//                ? Regions::getLiveRegions(null, intval($limit))
//                : [],
//            'live_categories' => \Palto\Categories::getLiveCategories(null, $this->region),
//            'breadcrumbs' => [],
//            'page' => $page
//        ]);
        echo $this->templatesEngine->make('garderobe');
    }

    private function redirect(string $url)
    {
        header('Location: ' . $url, true, 301);
    }
}