<?php
namespace App\ViewModels\Dance;

class BaseViewModel {
    public array $breadcrumbs = [];
    public $pageData;

    public function __construct($pageData = null) {
        $this->pageData = $pageData;
    }

    public function addBreadcrumb(string $label, ?string $url = null): void {
        $this->breadcrumbs[] = ['label' => $label, 'url' => $url];
    }
}