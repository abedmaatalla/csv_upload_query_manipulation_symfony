<?php

namespace App\Repository;

use App\Entity\Appel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Appel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appel[]    findAll()
 * @method Appel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appel::class);
    }

    public function findAllWithoutDureeTotal($date)
    {
        $old_date = explode('/', $date);
        $new_data = $old_date[1] . '/' . $old_date[0] . '/' . $old_date[2];

        return $this->createQueryBuilder('a')->andWhere("a.date_heure >= :date")
            ->setParameter('date', (new \DateTime('@' . strtotime($new_data)))->format('Y-m-d'))
            ->getQuery()->getResult();
    }

    public function top_volumes_factures($start_time, $end_time, $limit = 10)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select compte, sum(volume_facture) as volume_facture_sum from appel where TIME(`date_heure`) <= :end_time and TIME(`date_heure`) >= :start_time group by compte order by volume_facture_sum desc";
        if ($limit)
            $sql .= " limit $limit";
        $statement = $conn->prepare($sql);
        $statement->execute(['end_time' => $end_time, 'start_time' => $start_time]);

        return $statement->fetchAll();
    }

    public function totale_sms()
    {
        return $this->createQueryBuilder('a')
            ->select("COUNT('a') as sms_count", "a.id", 'a.compte')
            ->where("a.type = 'envoi de sms depuis le mobile' ")
            ->groupBy('a.compte')->getQuery()->getResult();
    }

    // /**
    //  * @return Appel[] Returns an array of Appel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Appel
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
