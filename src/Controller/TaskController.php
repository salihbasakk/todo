<?php

namespace App\Controller;

use App\Entity\Task;
use App\Event\DoneEvent;
use App\Form\TaskType;
use App\Repository\CategoryRepository;
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
    public function index(Request $request, TaskRepository $taskRepository, CategoryRepository $categoryRepository): Response
    {
        $categoryId = $request->get('category_id');

        $tasks = $taskRepository->findBy(["category" => $categoryId, "user" => $this->getUser(), "status" => '0']);

        if (isset($categoryId)) {
            $category = $categoryRepository->find($categoryId);

            return $this->render('task/index.html.twig', [
                'tasks' => $tasks,
                'category' => $category
            ]);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     */
    public function new(Request $request, CategoryRepository $categoryRepository, TaskRepository $taskRepository): Response
    {
        $categoryId = $request->get('category_id');

        $category = $categoryRepository->find($categoryId);

        $task = (new Task())
            ->setCategory($category)
            ->setUser($this->getUser())
            ->setStatus(0);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($task);
            $this->em->flush();

            $tasks = $taskRepository->findBy(["category" => $categoryId, "user" => $this->getUser(), "status" => '0']);

            return $this->render('task/index.html.twig', [
                'tasks' => $tasks,
                'category' => $category
            ]);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Task $task, CategoryRepository $categoryRepository): Response
    {
        $categoryId = $task->getCategory()->getId();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('task_index', [
                'id' => $task->getId(),
                'category_id' => $categoryId
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
    public function delete(Request $request, Task $task, TaskRepository $taskRepository, CategoryRepository $categoryRepository): Response
    {
        $categoryId = $task->getCategory()->getId();

        $category = $categoryRepository->find($categoryId);

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $this->em->remove($task);
            $this->em->flush();
        }

        $tasks = $taskRepository->findBy(["category" => $categoryId, "user" => $this->getUser(), "status" => '0']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'category' => $category
        ]);
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
