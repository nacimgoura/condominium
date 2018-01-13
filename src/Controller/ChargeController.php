<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\ChargeType;
use App\Form\PaymentType;
use App\Service\EmailService;
use App\Service\NotificationService;
use App\Service\PaymentService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @param Charge $charge
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDetail(Charge $charge) {
        return $this->render('charge/detail.html.twig', [
            'charge' => $charge
        ]);
    }

    /**
     * @Route("/charge/add", name="charge_add")
     * @param Request $request
     * @param PaymentService $paymentService
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCharge(Request $request, PaymentService $paymentService, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted('ROLE_MANAGER', null, 'Unable to access this page!');

        $charge = new Charge();

        $charge->setCondominium($this->getUser()->getCondominium());
        $charge->setAuthorizedUser($this->getUser()->getCondominium()->getUser());
        $charge->setUser($this->getUser());

        $form = $this->createForm(ChargeType::class, $charge,  [
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
            }

            $charge->setPayment($paymentService->generate($charge));

            $em = $this->getDoctrine()->getManager();
            $em->persist($charge);
            $em->flush();

            foreach($charge->getAuthorizedUser() as $user) {
                $notificationService->add('Charge', 'La charge "'.$charge->getTitle().'" vous à été affecté', $user);
            }

            $this->addFlash(
                'success',
                'Charge ajoutée avec succès!'
            );

            return $this->redirectToRoute('charge_index');
        }

        return $this->render('charge/formcharge.html.twig', [
            'charge' => $charge,
            'action' => $this->generateUrl('charge_add'),
            'form' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * @Route("/charge/edit/{id}", requirements={"id" = "\d+"}, name="charge_edit")
     * @param Request $request
     * @param Charge $charge
     * @param PaymentService $paymentService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editCharge(Request $request, Charge $charge, PaymentService $paymentService, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        if (!$charge || ($charge->getUser()->getId() != $this->getUser()->getId() && $this->getUser()->getCondominium())) {
            throw $this->createNotFoundException('Cette charge n\'existe pas');
        }

        $amountCharge = $charge->getAmount();
        $authorizedUser = clone $charge->getAuthorizedUser();

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
            } else if ($attachment) {
                $formCharge->setAttachment($attachment);
            }

            $formCharge->setAmount($amountCharge);

            $em = $this->getDoctrine()->getManager();

            // si on ajoute un nouvel utilisateur on supprime les anciens paiement
            if (count($formCharge->getAuthorizedUser()) != count($authorizedUser)) {
                $formCharge->setPayment($paymentService->generate($formCharge));
            }

            $em->persist($formCharge);
            $em->flush();

            $this->addFlash(
                'success',
                'Charge éditée avec succès!'
            );

            foreach($charge->getAuthorizedUser() as $user) {
                $notificationService->add('Charge', 'La charge "'.$charge->getTitle().'" a été édité', $user);
            }

            return $this->redirectToRoute('charge_index');
        }

        return $this->render('charge/formcharge.html.twig', [
            'charge' => $charge,
            'action' => $this->generateUrl('charge_edit', ['id' => $charge->getId()]),
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    /**
     * @Route("/charge/delete/{id}", requirements={"id" = "\d+"}, name="charge_delete")
     * @param Charge $charge
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCharge(Charge $charge, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        if (!$charge || ($charge->getUser()->getId() != $this->getUser()->getId() && $this->getUser()->getCondominium())) {
            throw $this->createNotFoundException('Cette charge n\'existe pas');
        }


        foreach($charge->getAuthorizedUser() as $user) {
            $notificationService->add('Charge', 'La charge "'.$charge->getTitle().'" a été supprimé', $user);
        }

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
     * @param Charge $charge
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payCharge(Request $request, Charge $charge, NotificationService $notificationService) {

        $payment = $this->getDoctrine()
            ->getRepository(Payment::class)
            ->findOneBy([
                'charge' => $charge->getId(),
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

                $notificationService->add('Charge', 'Vous avez payé '.$newAmountPaid.'€ pour la charge "'.$charge->getTitle().'"', $this->getUser());

                return $this->redirectToRoute('charge_detail', ['id' => $payment->getCharge()->getId()]);
            }
        }

        return $this->render('charge/formpayement.html.twig', [
            'payment' => $payment,
            'action' => $this->generateUrl('charge_pay', ['id' => $charge->getId() ]),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/charge/callforcapital/{id}", requirements={"id" = "\d+"}, name="charge_create_call_for_capital")
     * @param Charge $charge
     * @param NotificationService $notificationService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createCallForCapital(Charge $charge, NotificationService $notificationService) {

        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_MANAGER'], null, 'Unable to access this page!');

        foreach($charge->getUser() as $user) {
            $notificationService->add('Charge', 'Un appel de fond à été réalisé pour la charge "'.$charge->getTitle().'"', $user);
        }

        $this->addFlash(
            'success',
            'Appel de fond réalisé avec succès!'
        );

        return $this->redirectToRoute('charge_detail', ['id' => $charge->getId()]);
    }

}