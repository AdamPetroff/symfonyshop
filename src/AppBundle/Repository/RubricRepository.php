<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Rubric;
use Doctrine\ORM\EntityRepository;
use Stringy\StaticStringy;

class RubricRepository extends EntityRepository
{
    /**
     * @param Rubric $rubric
     */
    public function saveRubric(Rubric $rubric)
    {
        if (empty($rubric->getUrl())) {
            $this->assignUniqueUrl($rubric);
        }
        $this->getEntityManager()->persist($rubric);
        $this->getEntityManager()->flush();
    }

    /**
     * @return Rubric[]
     */
    public function findProper()
    {
        /**
         * @var Rubric[] $all
         */
        $all = $this->findAll();
        $count = count($all);
        for ($i = 0; $i < $count; $i++) {
            if ($all[$i]->getDeleted() == true) {
                unset($all[$i]);
            }
        }

        return $all;
    }

    /**
     * @param Rubric $rubric
     * @return Rubric[]
     */
    public function findNotUnderRubric(Rubric $rubric)
    {
        $rubrics = $this->findProper();
        $count = count($rubrics);
        $successors = $this->findSuccessorsOf($rubric);

        for ($i = 0; $i < $count; $i++) {
            if ($rubrics[$i]->getId() == $rubric->getId() || in_array($rubrics[$i], $successors)) {
                unset($rubrics[$i]);
            }
        }

        return $rubrics;
    }

    /**
     * @param Rubric $rubric
     * @return array
     */
    public function findSuccessorsOf(Rubric $rubric) : array
    {
        $callback = function (Rubric $rubric, callable $callback) use (&$array) {
            foreach ($rubric->getNonDeletedChildren() as $child) {
                if (!in_array($child, $array)) {
                    $array[] = $child;
                }
                $callback($child, $callback);
            }
        };
        $array = [];
        $callback($rubric, $callback);

        return $array;
    }

    /**
     * @return array
     */
    public function findRubricsWithoutParent()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.parent IS NULL')
            ->andWhere('r.deleted = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Rubric $rubric
     */
    public function assignUniqueUrl(Rubric $rubric)
    {
        $url = StaticStringy::slugify($rubric->getName());
        $count = $this->createQueryBuilder('r')
            ->andWhere("r.url = :url")
            ->setParameter('url', $url);

        if ($rubric->getId()) {
            $count->andWhere("r.id != :id")
                ->setParameter('id', $rubric->getId());
        }

        $count
            ->select('COUNT(r)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($count) {
            $url .= '-' . ($count + 1);
        }
        $rubric->setUrl($url);
    }
}