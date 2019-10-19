<?php
namespace Matiere\Repository;
use Doctrine\ORM\EntityRepository;
use Matiere\Entity\Matiere;
use Matiere\Entity\Discipline;
use Doctrine\ORM\Query;
/**
 * This is the custom repository class for Matiere entity.
 */
class MatiereRepository extends EntityRepository
{
    
    /**
     * Finds all published posts having any tag.
     * @return array
     */
    public function findAllMatieres()
    {
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('m')
            ->from(Matiere::class, 'm')
            ->orderBy('m.libele_matiere', 'DESC');
        $matieres = $queryBuilder->getQuery()->getResult();
        
        return $matieres;
    }
    
    public function findAllMatiereEvalueeClassePeriode($classe, $periode, $annee)
    {
        
    }
    public function findMatiereNotInClasse($classe, $periode)
    {
       $entityManager = $this->getEntityManager();        
       $qb  = $entityManager->createQueryBuilder();
       $qb2 = $qb;
       $qb2->select('mc.id')
           ->from('Enseignee\Entity\Enseignee', 'ms')
           ->Join('ms.classe', 'm')
           ->Join('ms.matiere', 'mc')
           ->Join('ms.periodeval', 'mp')
           ->where('m.id = ?1')
           ->andWhere('mp.id = ?2');

       $qb  = $entityManager->createQueryBuilder();
       $qb->select('mm')
           ->from('Matiere\Entity\Matiere', 'mm')
           ->where($qb->expr()->notIn('mm.id', $qb2->getDQL())
         );
        $qb->setParameter(1, $classe)
           ->setParameter(2, $periode);
        $query  = $qb->getQuery();

       return $query->getResult();
       
    }
    
    public function findAllMatiereNotEvaluate($classe, $periodeval, $anneescolaire){
        
       $entityManager = $this->getEntityManager();        
       $qb  = $entityManager->createQueryBuilder();
       $qb2 = $qb;
       $qb2->select('mt.id')
           ->from('Evaluation\Entity\Evaluation', 'ms')
           ->Join('ms.matiere', 'mt')
           ->Join('ms.classe', 'm')
           ->Join('ms.periodeval', 'mc')
           ->Join('ms.anneescolaire', 'ma')
           ->where('m.id = ?1')
           ->andWhere('mc.id = ?2')
           ->andWhere('ma.id = ?3');

       $qb  = $entityManager->createQueryBuilder();
       $qb->select('mm')
           ->from('Matiere\Entity\Matiere', 'mm')
           ->where($qb->expr()->notIn('mm.id', $qb2->getDQL())
         );
        $qb->setParameter(1, $classe);
        $qb->setParameter(2, $periodeval);
        $qb->setParameter(3, $anneescolaire);
        
        $query  = $qb->getQuery();

       return $query->getResult();
        
    }
    
     public function findDisciplinesHavingAnyMatieres()
    {
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('d')
            ->from(Discipline::class, 'd')
            ->join('d.matieres', 'm')
            ->where('m.rang = ?1')
            ->setParameter('1', 0);
        
        $disciplines = $queryBuilder->getQuery()->getResult();
        
        return $disciplines;
    }
    public function findDisciplinesHavingAnyMatieresEvalue($classe, $periodeval){
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('d')
            ->from(Discipline::class, 'd')
            ->join('d.matieres', 'dm')
            ->join('dm.enseignees', 'dme')
            ->join('dme.periodeval', 'dmep')
            ->join('dm.evaluations', 'dmepe')
            ->where('dm.rang = ?1')
            ->andWhere('dme.classe = ?2')
            ->andWhere('dmepe.periodeval = ?3')
            ->setParameter('1', 0)
            ->setParameter('2', $classe)
            ->setParameter('3', $periodeval);
        
        $disciplines = $queryBuilder->getQuery()->getResult();
        
        return $disciplines;
    }
    
     public function findAllMatiereInThisClasse($classe){
        $entityManager = $this->getEntityManager();
        
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $queryBuilder->select('m')
            ->from(Matiere::class, 'm')
            ->join('m.enseignees', 'me')
            ->where('me.classe = ?1')
            ->setParameter(1, $classe);
        
        return $queryBuilder->getQuery()->getResult();
    }
       
}

