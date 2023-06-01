<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;
use function PHPUnit\Framework\isEmpty;


/**
 * @extends ServiceEntityRepository<Item>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function save(Item $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Item $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search($params, $phrase)
    {
        $qb = $this->createQueryBuilder('item');
        $qb->select('item.name', 'item.price', 'item.image', 'item.id', 'item.title')
            ->join('item.category', 'category');

        if (!empty($params)) {
            $qb
                ->andWhere($qb->expr()->in('item.category', ':params'))
                ->setParameter('params', $params);
        }

        if (!empty($phrase)) {
            $qb
                ->andWhere($qb->expr()->like('item.name', ':phrase'))
                ->orWhere($qb->expr()->like('item.title',':phrase'))
                ->setParameter('phrase', '%' . $phrase . '%');
        }
        $qb
            ->andWhere('item.sold = :sold')
            ->setParameter('sold', 0);

        return $qb->getQuery()->getResult();
    }


}
