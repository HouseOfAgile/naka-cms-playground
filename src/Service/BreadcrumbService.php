<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

class BreadcrumbService
{
    private $breadcrumbs = [];

    /**
     * Initializes the breadcrumb trail with a default entry based on the type.
     *
     * @param string $type The type of breadcrumb trail to initialize (e.g., 'main', 'blog').
     * @return self
     */
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

    /**
     * Adds a breadcrumb to the trail.
     *
     * @param string $label The label of the breadcrumb (can be a translation key).
     * @param string|null $route The Symfony route name.
     * @param array $routeParams Parameters for the route.
     * @return self
     */
    public function addBreadcrumb(string $label, string $route = null, array $routeParams = []): self
    {
        $this->breadcrumbs[] = [
            'label' => $label,
            'route' => $route,
            'routeParams' => $routeParams,
        ];

        return $this;
    }

    /**
     * Reverses the order of the breadcrumbs.
     *
     * This is useful when breadcrumbs are added from the current page upwards,
     * and you want to display them from the root to the current page.
     *
     * @return self
     */
    public function reverseBreadcrumbs(): self
    {
        $this->breadcrumbs = array_reverse($this->breadcrumbs);
        return $this;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
	    /**
     * Clears all breadcrumbs from the trail.
     *
     * Useful for resetting the breadcrumb trail, especially during initialization.
     *
     * @return self
     */
    public function clearBreadcrumbs(): self
    {
        $this->breadcrumbs = [];
        return $this;
    }
}
