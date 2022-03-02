<?php

namespace HouseOfAgile\NakaCMSBundle\Menu;

use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Entity\MenuItem;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    private $factory;

    /** @var MenuRepository */
    protected $menuRepository;

    /**
     * Add any other dependency you need...
     */
    public function __construct(
        FactoryInterface $factory,
        MenuRepository $menuRepository
    ) {
        $this->factory = $factory;
        $this->menuRepository = $menuRepository;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mb-2 mb-lg-0');
        $mainMenu = $this->menuRepository->findOneBy(['name' => 'mainMenu']);
        if ($mainMenu) {
            foreach ($mainMenu->getMenuItems() as $key => $menuItem) {
                $thisMenuItem = $this->addMenuItem($menuItem, $menu, true);
                $thisMenuItem->setLinkAttribute('class', 'nav-link')
                    ->setAttribute('class', 'nav-item');
            }
        }

        return $menu;
    }

    public function createFooterMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'list-unstyled text-muted');
        $footerMenu = $this->menuRepository->findOneBy(['name' => 'footerMenu']);
        if ($footerMenu) {
            foreach ($footerMenu->getMenuItems() as $key => $menuItem) {
                $thisMenuItem = $this->addMenuItem($menuItem, $menu)
                    ->setLinkAttribute('class', 'footer-link');
            }
        }

        return $menu;
    }


    public function addMenuItem(MenuItem $menuItem, ItemInterface $menu, $toUpper = false)
    {
        switch ($menuItem->getType()) {
            case NakaMenuItemType::TYPE_PAGE:
                if ($menuItem->getPage() == null) {
                } else {
                    $childParameters = ['route' => 'page_view', 'routeParameters' => ['slug' => $menuItem->getPage()->getSlug()]];
                }
                break;
            case NakaMenuItemType::TYPE_ROUTE:
                $childParameters = ['route' => $menuItem->getRoute(), 'routeParameters' => $menuItem->getRouteParameters()];

                break;
            case NakaMenuItemType::TYPE_URL:
                $childParameters = ['uri' => $menuItem->getUri()];
                break;
            default:
                break;
        }
        $menuItemTitle = strlen($menuItem->getTranslatedName()) > 32 ? substr($menuItem->getTranslatedName(), 0, 32) . "..." : $menuItem->getTranslatedName();
        if ($toUpper) {
            return $menu->addChild(strtoupper($menuItemTitle), $childParameters);
        } else {
            // dd($menuItemTitle);
            return $menu->addChild(ucFirst(strtolower($menuItemTitle)), $childParameters);
        }
    }
}
