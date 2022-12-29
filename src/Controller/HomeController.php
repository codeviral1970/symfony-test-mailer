<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
      Request $request, 
      EntityManagerInterface $manager,
      MailerInterface $mailer
    ): Response
    {
      $contact = new Contact();
      
      $form = $this->createForm(ContactType::class, $contact);
      
      $form->handleRequest($request);
      
      $message = null;
      
      if($form->isSubmitted() && $form->isValid() ) {
        $contact = ($form->getData());
        $manager->persist($contact);
        $manager->flush();

        $email = (new Email())
        ->from($contact->getEmail())
        ->to ('contact@codeviral.fr')
        ->subject($contact->getSubject())
        ->text($contact->getContent());
        $mailer->send($email);
        //dd($mailer);

        $this->addFlash('success', 'Votre message à bien été envoyé');
        
        return $this->redirectToRoute('app_home');
        
      }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

  }