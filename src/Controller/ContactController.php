<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Create the email
            $email = (new Email())
                ->from($data['email'])
                ->to('imanebenamar22@gmail.com')
                ->subject('New Contact Form Submission')
                ->text(sprintf(
                    "You have received a new message from %s (%s).\n\nMessage:\n%s",
                    $data['tel'],
                    $data['email'],
                    $data['message']
                ));

            // Send the email
            // dd($email, "testtttt");
            $mailer->send($email);
            // try {
            //     dd('Sending email', $email);

            //     dd('Email sent');
            // } catch (\Exception $e) {
            //     dd("test");
            // }


            // Add a flash message or redirect to a thank you page
            $this->addFlash('success', 'Your message has been sent.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
