<?php

namespace App\Controller\Admin;

use App\Entity\AdminNotification;
use App\Form\AdminNotificationType;
use App\Form\CounterType;
use App\Message\CounterMessage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Umbrella\CoreBundle\Controller\BaseController;

/**
 * @Route("/notification")
 */
class NotificationController extends BaseController
{
    /**
     * @Route("/")
     */
    public function index(Request $request)
    {
        $notification = new AdminNotification();
        $notification->title = 'Hello !';

        $f1 = $this->createForm(AdminNotificationType::class, $notification);
        $f1->handleRequest($request);
        if ($f1->isSubmitted() && $f1->isValid()) {
            $this->persistAndFlush($notification);
            $this->toastSuccess('Notification sent');

            return $this->redirectToRoute('app_admin_notification_index');
        }

        $counter = new CounterMessage();

        $f2 = $this->createForm(CounterType::class, $counter);
        $f2->handleRequest($request);
        if ($f2->isSubmitted() && $f2->isValid()) {
            $this->dispatchMessage($counter);
            $this->toastSuccess('Counter started');

            return $this->redirectToRoute('app_admin_notification_index');
        }

        return $this->render('admin/notification/index.html.twig', [
            'new_notification_form' => $f1->createView(),
            'counter_form' => $f2->createView()
        ]);
    }
}
