<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\Staff;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Repository\MenuItemRepository;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use HouseOfAgile\NakaCMSBundle\Repository\PageBlockElementRepository;
use HouseOfAgile\NakaCMSBundle\Repository\StaffRepository;

class StaffManager
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var StaffRepository */
    protected $staffRepository;



    public function __construct(
        EntityManagerInterface $entityManager,
        StaffRepository $staffRepository
    ) {
        $this->entityManager = $entityManager;
        $this->staffRepository = $staffRepository;
    }

    /**
     * getStaffByBusinessRole function
     *
     * @param string $businessRole
     * @return Staff[]
     */
    public function getStaffByBusinessRole(string $businessRole)
    {
        return $this->staffRepository->findBy(['businessRole' => $businessRole, 'isActive' => true]);
    }
}
