<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Entity\StudentProfile;
use App\Enum\OfferStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobOffer>
 */
class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    /**
     * Retourne les offres actives non encore swipées par l'étudiant (ou dont le swipe a été annulé).
     *
     * @return JobOffer[]
     */
    public function findAvailableOffersForStudent(
        StudentProfile $studentProfile,
        ?string $rhythm = null,
        ?string $studyLevel = null,
        ?string $duration = null
    ): array {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.companyprofile', 'c')
            ->addSelect('c')
            ->andWhere('j.status = :status')
            ->setParameter('status', OfferStatus::ACTIVE);

        // Exclure les offres déjà swipées par cet étudiant (sauf si rewound)
        $swipedOffersQb = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(s.joboffer)')
            ->from('App\Entity\Swipe', 's')
            ->andWhere('s.studentprofile = :student')
            ->andWhere('s.isRewound = false');

        $qb->andWhere($qb->expr()->notIn('j.id', $swipedOffersQb->getDQL()))
            ->setParameter('student', $studentProfile);

        // Filtre par rythme si fourni
        if ($rhythm && $rhythm !== 'all') {
            $qb->andWhere('j.acceptedRhythm LIKE :rhythm')
                ->setParameter('rhythm', '%' . $rhythm . '%');
        }

        // Filtre par niveau d'études si fourni
        if ($studyLevel && $studyLevel !== 'all') {
            $qb->andWhere('j.requiredStudyLevel LIKE :studyLevel')
                ->setParameter('studyLevel', '%' . $studyLevel . '%');
        }

        // Filtre par durée si fournie
        if ($duration && $duration !== 'all') {
            $qb->andWhere('j.acceptedDurations LIKE :duration')
                ->setParameter('duration', '%' . $duration . '%');
        }

        return $qb->orderBy('j.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
