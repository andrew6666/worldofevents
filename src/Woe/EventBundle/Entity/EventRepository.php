<?php

namespace Woe\EventBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
    /**
     * Returns event list for the search term $keywords.
     * Event is included in the search results only if all keywords are matched.
     * @param array $keywords
     * @return Event[]
     */
    public function findByKeywords(array $keywords)
    {
        $query = $this->createQueryBuilder('event')
            ->innerJoin('event.keywords', 'k')
            ->where('event.date > :date AND k.name IN (:keywords)')
            ->groupBy('event.id')
            ->having("COUNT(event.id) = :count")
            ->orderBy('event.date')
            ->setParameters(array(
                'date' => new \DateTime('now'),
                'keywords' => $keywords,
                'count' => count($keywords)
            ));

        return $query->getQuery()->getResult();
    }

    /**
     * All future events sorted by date ascending
     *
     * @return Event[]
     */
    public function findAllActiveSortedByDate()
    {
        $query = $this->createQueryBuilder('event')
            ->where('event.date > :date')
            ->setParameter('date', new \DateTime('now'))
            ->orderBy('event.date');

        return $query->getQuery()->getResult();
    }

    /**
     * Find events by tag id
     *
     * @param $id
     * @return Event[]
     */
    public function findByTagId($id)
    {
        $query = $this->createQueryBuilder('event')
            ->innerJoin('event.tags', 't')
            ->where('t.id = :tag_id')
            ->setParameter('tag_id', $id);

        return $query->getQuery()->getResult();
    }

    /**
     * Find recent and upcoming events by current event's date (+/- 12 hours)
     * not including the event itself
     *
     * @param Event $event
     * @return array
     */
    public function findNearbyEvents(Event $event)
    {
        $interval = new \DateInterval('PT12H');
        $from = clone $event->getDate();
        $from->sub($interval);
        $to = clone $event->getDate();
        $to->add($interval);

        $query = $this->createQueryBuilder('event')
            ->innerJoin('event.location', 'location')
            ->where('location.latitude IS NOT NULL')
            ->andWhere('location.longitude IS NOT NULL')
            ->andWhere('event.date BETWEEN :from AND :to')
            ->andWhere('event.id != :event_id')
            ->setParameters(array('from' => $from, 'to' => $to, 'event_id' => $event->getId()));

        return $query->getQuery()->getResult();
    }
}
