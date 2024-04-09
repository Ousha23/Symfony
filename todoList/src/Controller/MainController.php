<?php

namespace App\Controller;

use App\Entity\Tasks;
use App\Repository\TasksRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MainController extends AbstractController
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager,CategoriesRepository $categoriesRepository, TasksRepository $tasksRepository): Response
    {
        $translatedMessage = $this->translator->trans('welcome_message');

        $currentDate = new \DateTimeImmutable('today');
        $startOfDay = $currentDate->setTime(0, 0, 0);
        $endOfDay = $currentDate->setTime(23, 59, 59);

        
        $taskRepository = $entityManager->getRepository(Tasks::class);
        $tasks = $taskRepository->createQueryBuilder('t')
            ->andWhere('t.due_date >= :startOfDay')
            ->andWhere('t.due_date <= :endOfDay')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->getQuery()
            ->getResult();
        return $this->render('main/index.html.twig'
            , [
                'categories' => $categoriesRepository->findBy([],
                ['id' => 'asc']),
                'tasks' => $tasks,
                'fromFunction' => 'todayTasks',
            ]
    );
    }

    
}
