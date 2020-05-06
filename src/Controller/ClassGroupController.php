<?php

namespace App\Controller;

use App\Entity\ClassGroup;
use App\Entity\Student;
use App\Entity\StudentPresence;
use App\Form\ClassGroupType;
use App\Repository\ClassGroupRepository;
use App\Security\CheckAccessRights;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Port\Csv\CsvWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/class")
 */
class ClassGroupController extends AbstractController
{
    /**
     * @Route("/", name="class_group_index", methods={"GET"})
     * @throws \App\Exception\UserException
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(ClassGroup::class)->getFindAllQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        $todayDate = new \DateTime();
        $todayMonth = $this->getInversedFormatedMonth($todayDate->format('n'));

        $months = [
            'Septembre', 'Octobre', 'Novembre', 'Décembre', 'Janvier',
            'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août'
        ];

        $position = array_search($todayMonth, $months);
        $passedMonths = array_slice($months, 0, $position, true);

        return $this->render('class_group/index.html.twig', [
            'pagination' => $pagination,
            'months' => $passedMonths
        ]);
    }

    /**
     * @Route("/new", name="class_group_new", methods={"GET","POST"})
     * @throws \App\Exception\UserException
     */
    public function new(Request $request): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $classGroup = new ClassGroup();
        $form = $this->createForm(ClassGroupType::class, $classGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($classGroup);
            $entityManager->flush();

            return $this->redirectToRoute('class_group_index');
        }

        return $this->render('class_group/form.html.twig', [
            'class_group' => $classGroup,
            'form' => $form->createView(),
            'update' => false,
        ]);
    }

    /**
     * @Route("/{id}", name="class_group_show", methods={"GET"})
     */
    public function show(ClassGroup $classGroup): Response
    {
        return $this->render('class_group/show.html.twig', [
            'class_group' => $classGroup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="class_group_edit", methods={"GET","POST"})
     * @throws \App\Exception\UserException
     */
    public function edit(Request $request, ClassGroup $classGroup): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $form = $this->createForm(ClassGroupType::class, $classGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('class_group_index', [
                'id' => $classGroup->getId(),
            ]);
        }

        return $this->render('class_group/form.html.twig', [
            'class_group' => $classGroup,
            'form' => $form->createView(),
            'update' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="class_group_delete", methods={"DELETE"})
     * @throws \App\Exception\UserException
     */
    public function delete(Request $request, ClassGroup $classGroup): Response
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        if ($this->isCsrfTokenValid('delete'.$classGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($classGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('class_group_index');
    }

    /**
     * @Route("{id}/generate/csv", name="class_group_export_csv", methods={"POST"})
     * @throws \App\Exception\UserException
     */
    public function exportAbsenceCsv(Request $request, ClassGroup $group = null)
    {
        CheckAccessRights::hasAdminOrSuperAdminRole($this->getUser());

        $em = $this->getDoctrine()->getManager();
        //$group = $em->getRepository(ClassGroup::class)->find(7);

        $month = $request->request->get("mois");
        $formatedMonth = $this->getFormatedMonth($month);
        $recap = [];

        $students = $group->getStudents();
        /** @var Student $student */
        foreach ($students as $student) {
            $studentPresences = $student->getPresences()->getValues();
            $studentPresencesForMonth = array_filter($studentPresences, function ($presence) use ($formatedMonth) {
                /** @var StudentPresence $presence */
                return $presence->getlesson()->getDateStart()->format('n') === $formatedMonth;
            });

            /** @var StudentPresence $presence */
            foreach ($studentPresencesForMonth as $presence) {
                $lesson = $presence->getlesson();
                $day = $lesson->getDateStart()->format('d');
                $isPresent = $presence->getPresent();
                $isLate = $presence->getLate();
                $studentIdentity = $student->getIdentity();

                if (!$isPresent) {
                    $absenceDurationByDay = $lesson->getDuration();
                    if ($presence->getExcuseValidated()) {
                        $recap[$month][$studentIdentity][$day]['absence']['justified'] = 'justifié';
                        $recap[$month][$studentIdentity][$day]['absence']['justified']['time'] += $absenceDurationByDay;

                    } else {
                        $recap[$month][$studentIdentity][$day]['absence']['unjustified'] = 'justifié';
                        $recap[$month][$studentIdentity][$day]['absence']['unjustified']['time'] += $absenceDurationByDay;
                    }
                }

                if ($isLate) {
                    $lateness = $lesson->getDateStart()->diff($presence->getLate());
                    if ($presence->getExcuseValidated()) {
                        $recap[$month][$studentIdentity][$day]['lateness']['justified'] = 'justifié';
                        $recap[$month][$studentIdentity][$day]['lateness']['justified']['time'] += $lateness;
                    } else {
                        $recap[$month][$studentIdentity][$day]['lateness']['unjustified'] = 'justifié';
                        $recap[$month][$studentIdentity][$day]['lateness']['unjustified']['time'] += $lateness;
                    }
                }
            }
        }

        $filename = $month . date("Y") . '.csv';

        $writer = new CsvWriter();
        $writer->setStream(fopen($filename, 'w'));

        //columns
        $writer->writeItem(array('Mois', 'Etudiant', 'Jour', 'Absence/retard', 'Justifié/non justifié', 'Temps'));
        $writer->writeItem($recap);
        $writer->finish();

        $response = new BinaryFileResponse($filename);
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        if ($mimeTypeGuesser->isSupported()){
            $response->headers->set('Content-Type', $mimeTypeGuesser->guess( $filename));
        } else {
            // Set the mimetype of the file manually, in this case for a csv file is text/csv
            $response->headers->set('Content-Type', 'text/csv');
        }

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }

    /** Ugly as hell, but works just fine.
     *  If someone take back this project, it could be a good idea to rework this
     */
    private function getFormatedMonth(String $month) : int
    {
        switch ($month) {
            case 'Janvier': {
                return 01;
            }
            case 'Février': {
                return 2;
            }
            case 'Mars': {
                return 3;
            }
            case 'Avril': {
                return 4;
            }
            case 'Mai': {
                return 5;
            }
            case 'Juin': {
                return 6;
            }
            case 'Juillet': {
                return 7;
            }
            case 'Août': {
                return 8;
            }
            case 'Septembre': {
                return 9;
            }
            case 'Octobre': {
                return 10;
            }
            case 'Novembre': {
                return 11;
            }
            case 'Décembre' : {
                return 12;
            }
        }

        return 0;
    }

    /** Ugly as hell, but works just fine.
     *  If someone take back this project, it could be a good idea to rework this
     */
    private function getInversedFormatedMonth(int $month) : String
    {
        switch ($month) {
            case 1: {
                return 'Janvier';
            }
            case 2: {
                return 'Février';
            }
            case 3: {
                return 'Mars';
            }
            case 4: {
                return 'Avril';
            }
            case 5: {
                return 'Mai';
            }
            case 6: {
                return 'Juin';
            }
            case 7: {
                return 'Juillet';
            }
            case 8: {
                return 'Août';
            }
            case 9 :{
                return 'Septembre';
            }
            case 10: {
                return 'Octobre';
            }
            case 11: {
                return 'Novembre';
            }
            case 12: {
                return 'Décembre';
            }
        }

        return 0;
    }
}
