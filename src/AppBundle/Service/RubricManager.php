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

    public function getRubricByUrl(string $url)
    {
        $rubric = $this->repository->findOneBy(['url' => $url]);
        if(!$rubric){
            //TODO - vytiahnut default rubriku z tabulky settings
            $rubric = $this->repository->find(1);
//            throw new NotFoundHttpException;
        }
        return $rubric;
    }

    public function getBaseRubrics()
    {
        return $this->repository->query('r', ['where' => 'r.parent is null']);
    }

    public function save(Rubric $rubric)
    {
        $this->repository->flush($rubric);
    }
}