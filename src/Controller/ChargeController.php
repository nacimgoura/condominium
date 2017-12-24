<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\ChargeType;
use App\Form\PaymentType;
use App\Service\PaymentService;
use Symfony\Component\Form\FormError;
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

        $form = $this->createForm(ChargeType::class, $charge, ['user' => $this->getUser()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $charge = $form->getData();

            $file = $form['attachment']->getData();
            if ($file) {
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move('attachment', $fileName);
                $charge->setAttachment($fileName);
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

        return $this->render('charge/add.html.twig', [
            'action' => $this->generateUrl('charge_add'),
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

        $form = $this->createForm(PaymentType::class, clone $payment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPayment = $form->getData();

            $newAmountPaid = $payment->getAmountPaid() + $newPayment->getAmountPaid();

            if ($newAmountPaid > $payment->getAmountTotal()) {
                $form->get('amountPaid')->addError(new FormError('La somme versée excède votre charge.'));
            } else {
                $payment->setAmountPaid($newAmountPaid);

                $file = $form['attachment']->getData();
                if ($file) {
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();
                    $file->move('attachment', $fileName);
                    $payment->setAttachment($fileName);
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

        return $this->render('charge/payement.html.twig', [
            'payment' => $payment,
            'action' => $this->generateUrl('charge_pay', ['id' => $id ]),
            'form' => $form->createView()
        ]);
    }

}