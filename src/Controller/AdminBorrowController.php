<?php

namespace App\Controller;

use App\Entity\Borrow;
use App\Form\BorrowType;
use App\Form\ConservationType;
use App\Repository\BorrowRepository;
use App\Repository\DocumentRepository;
use App\Service\BorrowService;
use App\Service\PenaltyService;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/borrow')]
class AdminBorrowController extends AbstractController
{
    #[Route('/', name: 'admin_borrow_index', methods: ['GET'])]
    public function index(BorrowRepository $borrowRepository): Response
    {
        return $this->render('back-office/borrow/index.html.twig', [
            'borrows' => $borrowRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_borrow_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $borrow = new Borrow();
        $form = $this->createForm(BorrowType::class, $borrow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($borrow);
            $entityManager->flush();

            return $this->redirectToRoute('admin_borrow_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/borrow/new.html.twig', [
            'borrow' => $borrow,
            'form' => $form,
        ]);
    }

    #[Route('/end-borrow/{id}', name: 'admin_borrow_end', methods: ['POST'], priority: 10)]
    public function endBorrow(Borrow $borrow, BorrowService $borrowService, Request $request, PenaltyService $penaltyService): Response
    {
        $conservation = $request->get('conservation');
        $malus = $borrowService->endBorrow($borrow, $conservation);

        $malus ? $penaltyService->calculatePenalty($malus, $borrow->getUser()) : NULL;


        /* $this->addFlash(
            'borrowStatus',
            'Emprunt terminé avec succès'
        ); */
        //return $this->redirectToRoute('admin_borrow_index', [], Response::HTTP_SEE_OTHER);
        return new JsonResponse(['message' => 'Emprunt terminé'], 200);
    }

    #[Route('/end-borrow-form', name: 'admin_borrow_end_form', methods: ['POST'], priority: 10)]
    public function endBorrowForm(BorrowRepository $borrowRepo, BorrowService $borrowService, Request $request, PenaltyService $penaltyService): Response
    {

        $ObjBorrow = new stdClass(); // objet php natif le plus basique, customizable - utilisé ici pour stocker l'id et le passer au formType
        $ObjBorrow->idBorrow = NULL;
        $ObjBorrow->conservation = NULL; // faire passer l'état de conservation de base du document

        $form = $this->createForm(ConservationType::class, $ObjBorrow); //associe les champs du form avec les proriété de mon objet fantoche
        $form->handleRequest($request); //hydrate mon objet avec les données qui viennent du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            // recoit données du formulaire
            $conservation = $ObjBorrow->conservation;
            $idBorrow = $ObjBorrow->idBorrow;
            $borrow = $borrowRepo->find($idBorrow);
            $malus = $borrowService->endBorrow($borrow, $conservation);

            $malus ? $penaltyService->calculatePenalty($malus, $borrow->getUser()) : NULL;


            $this->addFlash(
                'borrowStatus',
                'Emprunt terminé avec succès'
            );
            return $this->redirectToRoute('admin_borrow_show', ["id" => $idBorrow], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * Fonction qui sert à afficher 1 emprunt
     * Utilisation d'un objet fantoche pour hydrater le formulaire présent sur la page (id de l'emprunt et état de conservation du document)
     *
     * @param Borrow $borrow
     * @return Response
     */
    #[Route('/{id}', name: 'admin_borrow_show', methods: ['GET'])]
    public function show(Borrow $borrow, DocumentRepository $documentRepository): Response
    {

        // Récupération de la valeur de conservation du document emprunté
        $document = $documentRepository->find($borrow->getDocument());
        $conservation = $document->getConservation();

        $ObjBorrow = new stdClass(); // objet php natif le plus basique, customizable - utilisé ici pour stocker l'id et le passer au formType
        $ObjBorrow->idBorrow = $borrow->getId();
        $ObjBorrow->conservation = $conservation;

        $form = $this->createForm(ConservationType::class, $ObjBorrow);

        return $this->renderForm('back-office/borrow/show.html.twig', [
            'borrow' => $borrow,
            'form' => $form,
        ]);
    }



    #[Route('/{id}/edit', name: 'admin_borrow_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Borrow $borrow): Response
    {
        $form = $this->createForm(BorrowType::class, $borrow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_borrow_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back-office/borrow/edit.html.twig', [
            'borrow' => $borrow,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_borrow_delete', methods: ['POST'])]
    public function delete(Request $request, Borrow $borrow): Response
    {
        if ($this->isCsrfTokenValid('delete' . $borrow->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($borrow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_borrow_index', [], Response::HTTP_SEE_OTHER);
    }
}
