<?php

namespace HouseOfAgile\NakaCMSBundle\Menu;

use App\Entity\MenuItem;
use Doctrine\ORM\EntityRepository;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    protected $factory;

    /** @var EntityRepository */
    protected $entityRepository;

    /**
     * Add any other dependency you need...
     */
    public function __construct(
        FactoryInterface $factory,
        EntityRepository $entityRepository
    ) {
        $this->factory = $factory;
        $this->entityRepository = $entityRepository;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav mb-2 mb-lg-0');
        $mainMenu = $this->entityRepository->findOneBy(['name' => 'mainMenu']);
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
        $footerMenu = $this->entityRepository->findOneBy(['name' => 'footerMenu']);
        if ($footerMenu) {
            foreach ($footerMenu->getMenuItems() as $key => $menuItem) {
                $thisMenuItem = $this->addMenuItem($menuItem, $menu)
                    ->setLinkAttribute('class', 'footer-link');
            }
        }

        return $menu;
    }

    public function addMenuItem(MenuItem $menuItem, ItemInterface $menu, bool $toUpper = false)
    {
        switch ($menuItem->getType()) {
            case NakaMenuItemType::TYPE_PAGE:
                if ($menuItem->getPage() !== null) {
                    $childParameters = ['route' => 'page_view', 'routeParameters' => ['slug' => $menuItem->getPage()->getSlug()]];
                } else{
                    // not a valid menu item, we do not add it
                    return;
                }
                break;
            case NakaMenuItemType::TYPE_ROUTE:
                $childParameters = ['route' => $menuItem->getRoute(), 'routeParameters' => $menuItem->getRouteParametersAsArray()];

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
