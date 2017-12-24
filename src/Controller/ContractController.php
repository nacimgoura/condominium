<?php

namespace App\Controller;

use App\Form\ContractType;
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
     * @Route("/contract/{id}", name="contract_detail")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail($id) {
        $contract = $this->getDoctrine()
            ->getRepository(Contract::class)
            ->find($id);

        return $this->render('contract/detail.html.twig', [
            'contract' => $contract
        ]);
    }

    /**
     * @Route("/contract/add", name="contract_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addContract(Request $request) {

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
            return $this->redirectToRoute('contract_index');
        }

        return $this->render('contract/add.html.twig', [
            'action' => $this->generateUrl('contract_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contract/delete/{id}", name="contract_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContract($id) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        $contract = $this->getDoctrine()
            ->getRepository(Contract::class)
            ->find($id);

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
