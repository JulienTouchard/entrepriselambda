<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository, CompanyRepository $companyRepository): Response
    {
        if ($this->getUser() === Null) {
            return $this->redirectToRoute('app_login');
        } else {
            // je récupère l'idCompany de mon utilisateur
            $idCompanyUser = $this->getUser()->getCompany()->getId();
            // je récupère ici les information de la company de mon utilisateur  
            $nameCompany = $companyRepository->findBy(['id' => $idCompanyUser]);
            // je crée un tableau destiné à recevoir les projets à afficher à mon utilisateur
            $tbProject = $projectRepository->findBy(['owner'=>$this->getUser()->getCompany()]);
            
            return $this->render('project/index.html.twig', [
                // je renvoie ce tableau à ma vue
                'projects' => $tbProject,
                // ainsi que le nom de la company
                'nameCompany' => $nameCompany[0]->getName()
            ]);
        }
    }
    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // je dois récupere l'id de la company de mon user
            $dir = $project->getName();
            if (!file_exists("project/" . $dir)) mkdir("project/" . $dir);
            $userIdCompany = $this->getUser()->getCompany()->getId();
            $project->setOwner($this->getUser()->getCompany());
            //dd($userIdCompany);

            $project->setCreatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"POST"})
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }
}
