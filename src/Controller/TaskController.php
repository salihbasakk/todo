<?php

namespace App\Controller;

use App\Entity\Task;
use App\Event\DoneEvent;
use App\Event\MemcacheEvent;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /** @var EntityManagerInterface $em*/
    private $em;

    /**
     * TaskController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="task_index", methods={"GET"})
     */
    public function index(Request $request, TaskRepository $taskRepository, \Memcached $mem, EventDispatcherInterface $dispatcher): Response
    {
        $categoryId = $request->get('category_id');

        $memcacheKey = 'tasks' . $categoryId;

        if(!($tasks = $mem->get($memcacheKey))){
            $tasks = $taskRepository->findBy(["category" => $categoryId, "user" => $this->getUser(), "status" => '0']);

            $mem->set($memcacheKey, $tasks,1500);

        } elseif (!($tasks == $taskRepository->findBy(["category" => $categoryId, "user" => $this->getUser(), "status" => '0']))) {

            $event = new MemcacheEvent();

            $event->setMemcacheKey($memcacheKey);

            $dispatcher->dispatch(MemcacheEvent::NAME, $event);

            $tasks = $taskRepository->findBy(["category" => $categoryId, "user" => $this->getUser(), "status" => '0']);

            $mem->set($memcacheKey, $tasks,1500);
        }

        return $this->render('task/index.html.twig', [
            'tasks' =>$tasks
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($task);
            $this->em->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('task_index', [
                'id' => $task->getId(),
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $this->em->remove($task);
            $this->em->flush();
        }

        return $this->redirectToRoute('task_index');
    }

    /**
     * @Route("/{id}/done", name="task_done", methods={"GET"})
     */
    public function done(EventDispatcherInterface $dispatcher, Task $task): Response
    {
        $task->setStatus('1');

        $this->em->persist($task);
        $this->em->flush();

        $event = new DoneEvent();

        $event->setTask($task);

        $dispatcher->dispatch(DoneEvent::NAME, $event);

        return $this->render('donetask.html.twig');
    }
}
