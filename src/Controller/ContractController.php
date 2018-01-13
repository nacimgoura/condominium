<?php

namespace App\Controller;

use App\Form\ContractType;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Contract;

class ContractController extends Controller
{
    /**
     * @Route("/contract", name="contract_index")
     */
    public function index() {

        $listContract = $this->getDoctrine()
            ->getRepository(Contract::class)
            ->findOwnContract($this->getUser());

        return $this->render('contract/index.html.twig', [
            'listContract' => $listContract
        ]);
    }

    /**
     * @Route("/contract/{id}", requirements={"id" = "\d+"}, name="contract_detail")
     * @param Contract $contract
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail(Contract $contract) {
        return $this->render('contract/detail.html.twig', [
            'contract' => $contract
        ]);
    }

    /**
     * @Route("/contract/add", name="contract_add")
     * @param Request $request
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addContract(Request $request, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted('ROLE_MANAGER', null, 'Unable to access this page!');

        $contract = new Contract();

        $contract->setCondominium($this->getUser()->getCondominium());

        $form = $this->createForm(ContractType::class, $contract);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contract = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $contract->setAttachment($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($contract);
            $em->flush();

            $this->addFlash(
                'success',
                'Contrat ajoutée avec succès!'
            );

            foreach($contract->getCondominium()->getUser() as $user) {
                $notificationService->add('Contract', 'Un nouveau contract "'.$contract->getTitle().'" à été ajouté.', $user);
            }

            return $this->redirectToRoute('contract_index');
        }

        return $this->render('contract/form.html.twig', [
            'action' => $this->generateUrl('contract_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contract/edit/{id}", name="contract_edit", requirements={"id" = "\d+"})
     * @param Request $request
     * @param Contract $contract
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editContract(Request $request, Contract $contract, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        $attachment = $contract->getAttachment();
        $form = $this->createForm(ContractType::class, $contract);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contract = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $contract->setAttachment($fileName);
            } else if ($attachment) {
                $contract->setAttachment($attachment);
            }

            $em = $this->getDoctrine()->getManager();
            $em->merge($contract);
            $em->flush();

            $this->addFlash(
                'success',
                'Contrat modifié avec succès!'
            );

            foreach($contract->getCondominium()->getUser() as $user) {
                $notificationService->add('Contract', 'Le contract "'.$contract->getTitle().'" à été modifié.', $user);
            }

            return $this->redirectToRoute('contract_index');
        }

        return $this->render('contract/form.html.twig', [
            'action' => $this->generateUrl('contract_edit', ['id' => $contract->getId()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contract/delete/{id}", name="contract_delete", requirements={"id" = "\d+"})
     * @param Contract $contract
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContract(Contract $contract, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        foreach($contract->getCondominium()->getUser() as $user) {
            $notificationService->add('Contract', 'Le contract "'.$contract->getTitle().'" à été supprimé.', $user);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($contract);
        $em->flush();

        $this->addFlash(
            'success',
            'Contrat supprimée avec succès!'
        );

        return $this->redirectToRoute('contract_index');
    }
}
