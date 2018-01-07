<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\ChargeType;
use App\Form\PaymentType;
use App\Service\PaymentService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Charge;

class ChargeController extends Controller
{
    /**
     * @Route("/charge", name="charge_index")
     */
    public function index() {

        $listCharge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->findOwnCharge($this->getUser());

        return $this->render('charge/index.html.twig', [
            'listCharge' => $listCharge
        ]);
    }

    /**
     * @Route("/charge/{id}", requirements={"id" = "\d+"}, name="charge_detail")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail($id) {
        $charge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->find($id);

        return $this->render('charge/detail.html.twig', [
            'charge' => $charge
        ]);
    }

    /**
     * @Route("/charge/add", name="charge_add")
     * @param Request $request
     * @param PaymentService $paymentService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCharge(Request $request, PaymentService $paymentService) {

        $this->denyAccessUnlessGranted('ROLE_MANAGER', null, 'Unable to access this page!');

        $charge = new Charge();

        $charge->setCondominium($this->getUser()->getCondominium());

        $form = $this->createForm(ChargeType::class, $charge, [
            'charge' => $charge,
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $charge = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $charge->setAttachment($fileName);
                $charge->setListAttachment(array_merge($charge->getListAttachment(), [$charge->getAttachment()]));
            }

            $em = $this->getDoctrine()->getManager();

            $paymentService->generate($charge);
            $em->persist($charge);
            $em->flush();

            $this->addFlash(
                'success',
                'Charge ajoutée avec succès!'
            );
            return $this->redirectToRoute('charge_index');
        }

        return $this->render('charge/formcharge.html.twig', [
            'charge' => $charge,
            'action' => $this->generateUrl('charge_add'),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/charge/edit/{id}", requirements={"id" = "\d+"}, name="charge_edit")
     * @param Request $request
     * @param PaymentService $paymentService
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCharge(Request $request, PaymentService $paymentService, $id) {

        $charge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->findOneById($id);

        $attachment = $charge->getAttachment();
        $form = $this->createForm(ChargeType::class, $charge, [
            'charge' => $charge,
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formCharge = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $formCharge->setAttachment($fileName);
            } else {
                $formCharge->setAttachment($attachment);
            }

            $em = $this->getDoctrine()->getManager();

            $paymentService->generate($formCharge);
            $em->persist($formCharge);
            $em->flush();

            $this->addFlash(
                'success',
                'Charge éditée avec succès!'
            );
            return $this->redirectToRoute('charge_index');
        }

        return $this->render('charge/formcharge.html.twig', [
            'charge' => $charge,
            'action' => $this->generateUrl('charge_edit', ['id' => $id]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/charge/delete/{id}", requirements={"id" = "\d+"}, name="charge_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCharge($id) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        $charge = $this->getDoctrine()
            ->getRepository(Charge::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($charge);
        $em->flush();

        $this->addFlash(
            'success',
            'Charge supprimée avec succès!'
        );

        return $this->redirectToRoute('charge_index');
    }

    /**
     * @Route("/charge/pay/{id}", requirements={"id" = "\d+"}, name="charge_pay")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payCharge(Request $request, $id) {

        $payment = $this->getDoctrine()
            ->getRepository(Payment::class)
            ->findOneBy([
                'charge' => $id,
                'user' => $this->getUser()
            ]);

        // on rempli l'input avec le montant restant à payer
        $formPayment = clone $payment;

        $formPayment->setAmountPaid($payment->getAmountTotal() - $payment->getAmountPaid());

        $form = $this->createForm(PaymentType::class, $formPayment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPayment = $form->getData();

            $newAmountPaid = $payment->getAmountPaid() + $newPayment->getAmountPaid();

            if (number_format($newAmountPaid) > number_format($payment->getAmountTotal())) {
                $form->get('amountPaid')->addError(new FormError('La somme versée excède votre charge.'));
            } else {
                $payment->setAmountPaid($newAmountPaid);

                $file = $form['attachment']->getData();
                if ($file) {
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move('attachment', $fileName);
                    $payment->setListAttachment(array_merge($payment->getListAttachment(), [$fileName]));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($payment);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Payement effectué avec succès!'
                );
                return $this->redirectToRoute('charge_detail', ['id' => $payment->getCharge()->getId()]);
            }


        }

        return $this->render('charge/formpayement.html.twig', [
            'payment' => $payment,
            'action' => $this->generateUrl('charge_pay', ['id' => $id ]),
            'form' => $form->createView()
        ]);
    }

}