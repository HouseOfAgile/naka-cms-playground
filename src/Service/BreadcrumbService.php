<?php

namespace  HouseOfAgile\NakaCMSBundle\Service;

class BreadcrumbService
{
    private $breadcrumbs = [];


    public function initialize(string $type = 'main'): self
    {
        if ($type === 'main') {
            $this->addBreadcrumb('breadCrumb.home', 'app_homepage');
            // You could add another part for breadcrumb generation
            // } elseif ($type === 'blog') {
            // $this->addBreadcrumb('breadCrumbBlog.home', 'blog_homepage');
        }

        return $this;
    }
    public function addBreadcrumb(string $label, string $route = null, array $routeParams = []): self
    {
        $this->breadcrumbs[] = [
            'label' => $label,
            'route' => $route,
            'routeParams' => $routeParams,
        ];

        return $this;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
