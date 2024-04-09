<?php

namespace App\Controller;

use Exception;
use App\Entity\Tasks;
use App\Form\TasksFormType;
use App\Repository\TasksRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tasks', name: 'tasks_')]
class TasksController extends AbstractController
{
    #[Route('/', name: 'all_tasks')]
    public function index(EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response
    {
       $taskRepository = $entityManager->getRepository(Tasks::class);
       $categories = $categoriesRepository->findAll();
       $tasks = $taskRepository->findAll();

       return $this->render('tasks/index.html.twig', [
           'tasks' => $tasks,
           'categories' => $categories,
           'fromFunction' => 'allTasks',
       ]);
    }
    #[Route('/done', name: 'done_tasks')]
    public function getDoneTasks(EntityManagerInterface $entityManager,TasksRepository $tasksRepository, CategoriesRepository $categoriesRepository): Response
    {
       $categories = $categoriesRepository->findAll();
       $tasks = $tasksRepository->findBy(['status' => 'Fait']);

       return $this->render('tasks/done.html.twig', [
           'tasks' => $tasks,
           'categories' => $categories,
           'fromFunction' => 'getDoneTasks',
       ]);
    }
    #[Route('/table', name: 'all_tasks_table')]
    public function tasksTable(EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response
    {
       $taskRepository = $entityManager->getRepository(Tasks::class);
       $categories = $categoriesRepository->findAll();
       $tasks = $taskRepository->findAll();

       return $this->render('tasks/tableTasks.html.twig', [
           'tasks' => $tasks,
           'categories' => $categories,
       ]);
    }
    #[Route('/add', name: 'add_task')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        $task = new Tasks();

        $taskForm = $this->createForm(TasksFormType::class, $task);
        //on traite la requete 
        $taskForm->handleRequest($request);

        //dd($taskForm);
        //on verifie si le formulaire est soumi et valide
        if($taskForm->isSubmitted() && $taskForm->isValid()){
    
            $user = $this->getUser(); 
            $createdAt = new \DateTimeImmutable();

            $task->setUser($user);
            $task->setCreatedAt($createdAt);
            try {
                $entityManager->persist($task);
                $entityManager->flush();
                $titleTask = $task->getTitle();
                $dueDateTask = $task->getDueDate();
                if ($dueDateTask) $dateString = $dueDateTask->format('Ymd\THms');
                else $dateString = $task->getCreatedAt()->format('Ymd\THms');
                //dd($dateString);
                $descriptTask = $task->getDescription();
                $this->addFlash('success', 'Tâche ajoutée avec succès. <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text='.$titleTask.'&dates='.$dateString.'/'.$dateString.'&details='.$descriptTask.'&location=My+Location&recurrence=RRULE:FREQ=WEEKLY;INTERVAL=1;COUNT=10"> Ajouter à mon calendrier </a>');
            } catch (Exception $e){
                $this->addFlash('danger', 'Impossible d\'ajouter la tâche');
            }
            
            //on redirige
            return $this->redirectToRoute('app_main');
        }

        return $this->renderForm('tasks/add.html.twig', compact('taskForm'));
    }

    #[Route('/edit/{id}', name: 'edit_task')]
    public function edit(Tasks $task, Request $request, EntityManagerInterface $entityManager):Response
    {

        // on crée le formulaire
        $taskForm = $this->createForm(tasksFormType::class, $task);
        try {
            //on traite la requete 
            $taskForm->handleRequest($request);

            //dd($taskForm);
            //on verifie si le formulaire est soumi et valide
            if($taskForm->isSubmitted() && $taskForm->isValid()){
                    $entityManager->persist($task);
                    $entityManager->flush();
                    $this->addFlash('success', 'Tâche modifiée avec succès');
                return $this->redirectToRoute('app_main');
            }
        } catch (Exception $e) {
            $this->addFlash('danger', 'Un problème est survenu, la tâche n\'a pas été modifié.');
        }
        
        return $this->renderForm('tasks/edit.html.twig', compact('taskForm'));
    }

    #[Route('/delete/{id}', name: 'delete_task')]
    public function delete(TaskS $task, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        // on supprime la tâche de la bdd
        $entityManager->remove($task);
        $entityManager->flush();
        $this->addFlash('success', 'Tâche supprimée avec succès');

        $referer = $request->headers->get('referer');
        
        return $this->redirect($referer);
    }

    #[Route('/finish/{id}', name: 'finish_task')]
    public function finishTask(Tasks $task, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $task->setStatus('Fait');
        // $idCategory = $task->getCategory()->getId();
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a été marquée comme terminée avec succès.');
        //on recupère le page qui envoi
        $referer = $request->headers->get('referer');
        
        return $this->redirect($referer);
}
}
