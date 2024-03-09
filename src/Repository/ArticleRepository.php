<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 30;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function getArticlesPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }

    public function getArticleAfterId(int $id): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id > :id')
            ->setParameter('id', $id)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getArticleBeforeId(int $id): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id < :id')
            ->setParameter('id', $id)
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param Category $category
     * @param int $sample
     * @return array
     */
    public function getArticlesByCategory(Category $category, int $sample = 5): array
    {
        $qry = $this->createQueryBuilder('a')
            ->andWhere('a.category = :category')
            ->setParameter('category', $category)
            ->orderBy('a.id', 'ASC');

        if ($sample > 0) {
            $qry->setMaxResults($sample);
        }

        return $qry
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws Exception
     */
    public function findByArticle(?string $q): array
    {
        if ($q === null) {
            return [];
        }

        $sql = "
            select a.*
            from article a
            where a.id like '%$q%'
            or a.label like '%$q%'
            or a.couleur like '%$q%'
            or a.taille like '%$q%'
            or a.pointure like '%$q%'
            or a.keywords like '%$q%'
            order by a.created_at desc
        ";

        $entityManager = $this->getEntityManager();

        $cnx = $entityManager->getConnection();

        $results = $cnx->executeQuery($sql);

        return $results->fetchAllAssociative();
    }

}
