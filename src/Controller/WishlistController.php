<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\WishlistLine;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\WishlistLineRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class WishlistController extends AbstractController
{
    #[Route('/', name: 'app_wishlist_index')]
    public function index(UserRepository $userRepository, WishlistRepository $wishlistRepository,
                          CategoryRepository $categoryRepository): Response
    {
        //Get the current user
        $user = $userRepository->find($this->getUser()->getId());

        //Check if this user already has a wishlist
        $wishlist = $wishlistRepository->findOneBy(['customer' => $user, 'checkedOutAt' => null, 'deletedAt' => null]);

        $categories = $categoryRepository->getCategories();

        return $this->render('cart/index.html.twig', [
            'wishlist' => $wishlist,
            'categories' => $categories,
        ]);
    }

    #[Route('/add/{id}', name: 'app_wishlist_add')]
    public function addCart(Article $article, UserRepository $userRepository, EntityManagerInterface $entityManager,
                            WishlistRepository $wishlistRepository, WishlistLineRepository $wishlistLineRepository): Response
    {
        //Get the current user
        $user = $userRepository->find($this->getUser()->getId());

        //Check if this user already has a wishlist
        $wishlist = $wishlistRepository->findOneBy(['customer' => $user, 'checkedOutAt' => null, 'deletedAt' => null]);

        //If not, create a new wishlist
        if (!$wishlist) {
            $wishlist = new Wishlist();
            $wishlist->setCustomer($user);
            $user->addWishlist($wishlist); //Add the wishlist to the user (bidirectional relationship)
            $entityManager->persist($wishlist);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        //Check if the article is already in the wishlist
        $wishListLine = $wishlistLineRepository->findOneBy(['wishlist' => $wishlist, 'article' => $article]);

        //If not, add the article to the wishlist
        if (!$wishListLine) {
            $wishListLine = new WishlistLine();
            $wishListLine->setArticle($article);
            $wishListLine->setWishlist($wishlist);
            $wishListLine->setQuantity(1);
        }
        else {
            $qty = $wishListLine->getQuantity();
            $wishListLine->setQuantity($qty + 1);
        }
        $entityManager->persist($wishListLine);
        $entityManager->flush();

        return $this->redirectToRoute('app_wishlist_nb_items');
    }

    #[Route('/nb-items', name: 'app_wishlist_nb_items')]
    public function getNbItemInCart(WishlistLineRepository $wishlistLineRepository, UserRepository $userRepository,
                                    WishlistRepository $wishlistRepository): Response
    {
        //Get the current user
        $user = $userRepository->find($this->getUser()->getId());

        //Check if this user already has a wishlist
        $wishlist = $wishlistRepository->findOneBy(['customer' => $user, 'checkedOutAt' => null, 'deletedAt' => null]);

        $wishlistLines = $wishlistLineRepository->findBy(['wishlist' => $wishlist]);

        return $this->render('cart/cart-icon.html.twig', [
            'nb' => count($wishlistLines),
        ]);
    }
}