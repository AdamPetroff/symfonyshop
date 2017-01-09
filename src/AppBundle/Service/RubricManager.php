<?php
/**
 * Created by Adam The Great.
 * Date: 23. 12. 2016
 * Time: 16:55
 */

namespace AppBundle\Service;

use AppBundle\Entity\Rubric;
use AppBundle\Repository\RubricRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RubricManager
{
    /**
     * @var RubricRepository
     */
    protected $repository;

    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository(Rubric::class);
    }

    /**
     * @param string $url
     * @return Rubric
     */
    public function getRubricByUrl(string $url)
    {
        $rubric = $this->repository->findOneBy(['url' => $url]);

        return $rubric;
    }

    /**
     * @param int $rubricId
     * @return Rubric
     */
    public function getRubric(int $rubricId): Rubric
    {
        return $this->repository->find($rubricId);
    }

    /**
     * @return Rubric[]
     */
    public function getBaseRubrics()
    {
        return $this->repository->findRubricsWithoutParent();
    }

    /**
     * @param Rubric $rubric
     */
    public function save(Rubric $rubric)
    {
        $this->repository->saveRubric($rubric);
    }

    /**
     * @return Rubric[]
     */
    public function getRubricsList()
    {
        $baseRubrics = $this->getBaseRubrics();
        $array = [];
        $callback = function (Rubric $rubric, $callback) use (&$array) {
            foreach ($rubric->getChildren() as $child) {
                if (!in_array($rubric, $array)) {
                    $array[$rubric->getId()] = $rubric;
                }
                $callback($child, $callback);
            }
        };
        foreach ($baseRubrics as $baseRubric) {
            $array[$baseRubric->getId()] = $baseRubric;
            $callback($baseRubric, $callback);
        }

        return $array;
    }

    /**
     * @param Rubric $rubric
     * @return Rubric[]
     */
    public function getPotentialParents(Rubric $rubric)
    {
        return $this->repository->findNotUnderRubric($rubric);
    }
}