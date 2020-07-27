<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Routing\Annotation\Route;



class MailController extends AbstractController
{
    /**
     * @Route("/booking", name="booking")
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $defaultData = ['message' => 'Comment ca va ???'];
        $form = $this->createFormBuilder($defaultData,['method' => Request::METHOD_POST])
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('nombre', NumberType::class)
            ->add('send', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $nom=$data['name'];
            $email=$data['email'];
            $nombre=$data['nombre'];

            $email = (new Email())
                ->from('charles.chevreuil@gmail.com')
                ->to($email)
                ->subject('Votre place est réserver')
                ->html('<h1>Bonjour,' . $nom . '</h1>
                        <p>voici vos ' . $nombre . ' ticket</p>');

            $mailer->send($email);

            return $this->render('booking.html.twig', [
                'form' => $form->createView(),
                'message'=>'Votre réservatin à bien été prise en compte! '
            ]);
        }

        return $this->render('booking.html.twig', [
            'form' => $form->createView(),
            'message'=>' '
        ]);

    }
}
