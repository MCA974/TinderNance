<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Entity\StudentProfile;
use App\Entity\Swipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Swipe>
 */
class SwipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Swipe::class);
    }

    /**
     * Trouve le dernier swipe actif (non annulé) d'un étudiant pour permettre le rewind.
     */
    public function findLatestStudentSwipe(StudentProfile $studentProfile): ?Swipe
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.studentprofile = :student')
            ->andWhere('s.isRewound = false')
            ->setParameter('student', $studentProfile)
            ->orderBy('s.studentSwipedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Vérifie si un swipe existe déjà entre un étudiant et une offre.
     */
    public function findExistingSwipe(StudentProfile $studentProfile, JobOffer $jobOffer): ?Swipe
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.studentprofile = :student')
            ->andWhere('s.joboffer = :offer')
            ->setParameter('student', $studentProfile)
            ->setParameter('offer', $jobOffer)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
