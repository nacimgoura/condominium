<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Sondage;
use App\Form\AnswerType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AnswerController extends Controller
{

    /**
     * @Route("sondage/{sondageId}/answer/add", requirements={"id" = "\d+"}, name="answer_add")
     * @param $sondageId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAnswer($sondageId, Request $request) {

        $answer = new Answer();

        $sondage = $this->getDoctrine()
            ->getRepository(Sondage::class)
            ->find($sondageId);

        $answer->setUser($this->getUser());
        $answer->setSondage($sondage);

        $form = $this->createForm(AnswerType::class, $answer, ['sondage' => $sondage]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $oldAnswer =  $this->getDoctrine()
                    ->getRepository(Answer::class)
                    ->findOneBy([
                        'sondage' => $sondageId,
                        'user' => $this->getUser()
                    ]);

                $answer = $form->getData();

                if ($oldAnswer && $oldAnswer->getId()) {
                    $oldAnswer->setTitle($answer->getTitle());
                    $em->merge($oldAnswer);
                } else {
                    $em->persist($answer);
                }

                $em->flush();

                $this->addFlash(
                    'success',
                    'Réponse ajouté avec succès!'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Une erreur s\'est produite!'
                );
            }

            return $this->redirectToRoute('sondage_detail', [ 'id' => $sondage->getId() ]);
        }

        return $this->render('sondage/answer.html.twig', [
            'action' => $this->generateUrl('answer_add', [ 'sondageId' => $sondage->getId() ]),
            'form' => $form->createView()
        ]);
    }
}