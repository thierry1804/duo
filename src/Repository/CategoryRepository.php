<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws Exception
     */
    public function getCategories(): array
    {
        $sql = "
            select c.id, c.label, count(a.id) as nb
            from article a
            inner join category c on c.id = a.category_id
            group by c.id
            having count(a.id) > 0
            order by c.label asc
        ";

        $entityManager = $this->getEntityManager();

        $cnx = $entityManager->getConnection();

        $results = $cnx->executeQuery($sql);

        return $results->fetchAllAssociative();
    }

    /**
     * @param string|null $q
     * @return array
     * @throws Exception
     */
    public function findByCategory(?string $q): array
    {
        if ($q === null) {
            return [];
        }

        $sql = "
            select c.id, c.label, count(a.id) as nb
            from article a
            inner join category c on c.id = a.category_id
            where c.label like '%$q%' or c.description like '%$q%'
            group by c.id
            having count(a.id) > 0
            order by c.label asc
        ";

        $entityManager = $this->getEntityManager();

        $cnx = $entityManager->getConnection();

        $results = $cnx->executeQuery($sql);

        return $results->fetchAllAssociative();
    }
}
