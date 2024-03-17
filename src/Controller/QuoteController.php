<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\QuoteType;
use App\Repository\UserRepository;
use App\Repository\WishlistLineRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/quote')]
class QuoteController extends AbstractController
{
    #[Route('/{id}', name: 'app_quote', requirements: ['id' => '\d+'])]
    public function index(Wishlist $wishlist, Request $request, EntityManagerInterface $entityManager,
                          UserRepository $userRepository): Response
    {
        $form = $this->createForm(QuoteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            list($username, $domain) = explode('@', $form->get('email')->getData());
            $mxRecords = dns_get_record($domain, DNS_MX);
            if (count($mxRecords) === 0) {
                $form->get('email')->addError(new FormError('L\'adresse email semble ne pas exister.'));
            }
            elseif (!preg_match('/^(\+\d{2,3}|0)\d{9}$/', $form->get('phone')->getData())) {
                $form->get('phone')
                    ->addError(new FormError('Veuillez vérifier le numéro de téléphone: il semble incorrect.'));
            }
            elseif ($form->get('transitaire')->getData() === 'other_transitaire'
                && $form->get('other_transitaire')->getData() === null) {
                $form->get('other_transitaire')->addError(new FormError('Veuillez saisir le nom de votre transitaire'));
            }
            elseif ($form->get('transitaire')->getData() === 'duo_transitaire'
                && $form->get('other_transitaire')->getData() !== null) {
                $form->get('other_transitaire')
                    ->addError(new FormError('Nous vous prions de ne pas saisir le nom d\'un transitaire si
                    vous avez choisi le transitaire partenaire de DUO.'));
            }
            else {
                //update current user
                $user = $userRepository->find($this->getUser()->getId());
                $user->setNom($form->get('nom')->getData());
                $user->setPrenom($form->get('prenom')->getData());
                $user->setTelephone($form->get('phone')->getData());
                $user->setEmail($form->get('email')->getData());
                $user->setAdresse($form->get('adresse')->getData());

                //update Wishlist
                $wishlist->setCheckedOutAt(new \DateTimeImmutable());
                $wishlist->setExpeditionMode($form->get('expedition')->getData());
                $wishlist->setTransitType($form->get('transitaire')->getData());
                $wishlist->setTransit($form->get('other_transitaire')->getData());

                //persist
                $entityManager->persist($user);
                $entityManager->persist($wishlist);
                $entityManager->flush();

                $this->addFlash('success', 'Votre demande de devis a bien été enregistrée.
                Nous vous contacterons dans les plus brefs délais.');

                return $this->redirectToRoute('app_wishlist_index');
            }
        }

        return $this->render('quote/index.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/list', name: 'app_quotes')]
    public function getQuotes(WishlistRepository $wishlistRepository): Response
    {
        $quotes = $wishlistRepository->findQuotes();
        return $this->render('quote/quotes.html.twig', [
            'quotes' => $quotes,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/view/{id}', name: 'app_quote_view', requirements: ['id' => '\d+'])]
    public function viewQuote(?Wishlist $wishlist): Response
    {
        if ($wishlist === null) {
            return new Response('404 Not Found', 404, ['Content-Type' => 'text/plain']);
        }

        return $this->render('quote/view.html.twig', [
            'wishlist' => $wishlist,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/save', name: 'app_quote_save', methods: ['POST'])]
    public function saveQuote(Request $request, WishlistRepository $wishlistRepository,
                              EntityManagerInterface $entityManager, WishlistLineRepository $lineRepository): Response
    {
        $datas = $request->request->all();
        $quoteId = $datas['quote_id'];
        $wishlist = $wishlistRepository->find($quoteId);
        $wishlist->setExpeditionCost($datas['expedition']);
        $wishlist->setTransitCost($datas['transit']);
        $wishlist->setCommission($datas['commission']);
        $wishlist->setQuotedAt(new \DateTimeImmutable());

        foreach ($datas['lines'] as $line) {
            $wishlistLine = $lineRepository->find($line['id']);
            $wishlistLine->setAmount($line['pu']);
            $wishlistLine->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($wishlistLine);
        }

        $entityManager->persist($wishlist);
        $entityManager->flush();

        return $this->redirectToRoute('app_quote_view', ['id' => $quoteId]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/print/{id}', name: 'app_quote_print', requirements: ['id' => '\d+'])]
    public function printQuote(Wishlist $wishlist): Response
    {
        if ($wishlist === null) {
            return new Response('404 Not Found', 404, ['Content-Type' => 'text/plain']);
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $domPdf = new Dompdf($options);
        $domPdf->loadHtml($this->renderView('quote/print.html.twig', [
            'wishlist' => $wishlist,
        ]));
        $domPdf->setPaper('A4', 'landscape');
        $domPdf->render();

        $today = new \DateTime();

        return new Response(
            $domPdf->stream('devis_' . $today->format('YmdHis'), ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
}
