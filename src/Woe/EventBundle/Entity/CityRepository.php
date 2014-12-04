<?php

namespace Woe\EventBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CityRepository extends EntityRepository
{
    /**
     * Find city by name or create new if it does not exist
     *
     * @param $name
     * @return City
     */
    public function findOrCreateCity($name)
    {
        $city = $this->findOneBy(array('name' => $name));

        if (is_null($city)) {
            $city = new City();
            $city->setName($name);
        }

        return $city;
    }
}