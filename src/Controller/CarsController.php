<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\AdRepository;
use App\Repository\CarRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CarsController extends AbstractController
{
    /**
     * @var CarRepository
     */
    private $carRepository;
    /**
     * @var AdRepository
     */
    private $adRepository;

    public function __construct(
        CarRepository $carRepository,
        AdRepository $adRepository
    )
    {
        $this->carRepository = $carRepository;
        $this->adRepository = $adRepository;
    }

    /**
     * @Route("/ads", name="ads", methods={"GET"})
     */
    public function getAds(): Response
    {
        try {
            $cars_brands = $this->carRepository->getCarsBrands();
            $cars_manufacturer = $this->carRepository->getCarsManufacturer();
            $cars_color = $this->carRepository->getCarsColor();
            return $this->render('ads/ads_list.html.twig', [
                'controller_name' => 'CarsController',
                'cars_brands' => $cars_brands,
                'cars_manufacturer' => $cars_manufacturer,
                'cars_color' => $cars_color,
            ]);
        } catch (Exception $exception) {
            return $this->render('error.html.twig', [
                'controller_name' => 'CarsController',
            ]);
        }
    }

    /**
     * @Route("/ads/list", name="ads_list", methods={"GET"})
     */
    public function getAdsList(Request $request): JsonResponse
    {
        $response = new JsonResponse();
        try {
            $ads = $this->adRepository->getAds($request->query->all());
            foreach ($ads as $loop => $ad) {
                $ads[$loop]['avatar'] = '/uploads/' . $ad['car_id'] . '/avatar.jpeg';
            }
            $response->setData($ads);
        } catch (Exception $exception) {
            $response->setData(['error' => 'Сервис недоступен. Попробуйте попытку позже']);
            $response->setStatusCode(503);
        }
        return $response;
    }

    /**
     * @Route("/ads/{ad_uuid?null}", name="ad_profile", methods={"GET"})
     */
    public function getAd($ad_uuid): Response
    {
        try {
            if ($ad = $this->adRepository->findOneBy(['ad_uuid' => $ad_uuid])) {
                return $this->render('ads/ad.html.twig', [
                    'controller_name' => 'CarsController',
                    'ad' => $ad->getAdParams(),
                ]);
            }
            return $this->render('error404.html.twig');
        } catch (Exception $exception) {
            return $this->render('error.html.twig', [
                'controller_name' => 'CarsController',
            ]);
        }
    }

    /**
     * @Route("/ad", name="ad_add", methods={"POST"})
     */
    public function addAd(
        Request $request,
        ValidatorInterface $validator
    ): Response
    {
        $response = new JsonResponse();
        try {
            $serializer = new Serializer(
                [new ObjectNormalizer()],
                ['json' => new JsonEncoder()]
            );
            $car = $serializer->deserialize(
                json_encode($request->request->all(), JSON_UNESCAPED_UNICODE),
                Car::class,
                'json'
            );
            $car->setAvatar($request->files->get('avatar'));
            $errors = $validator->validate($car);
            if (! $errors->count()) {
                $ad = $this->carRepository->createWithAd($car);
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $car->getId();
                $car->getAvatar()->move($destination, 'avatar.jpeg');
                $response->setData($ad->getAdParams());
            } else {
                $message = [];
                foreach ($errors as $error) {
                    if (!isset($message[$error->getPropertyPath()])) {
                        $message[$error->getPropertyPath()] = $error->getMessage();
                    }
                }
                $response->setData(['error' => $message]);
                $response->setStatusCode(422);
            }
        } catch (Exception $exception) {
            $response->setData(['error' => ['Сервис недоступен. Попробуйте попытку позже']]);
            $response->setStatusCode(503);
        }
        return $response;
    }

    /**
     * @Route("/ad/edit", name="ad_change", methods={"POST"})
     */
    public function changeAd(
        Request $request,
        ValidatorInterface $validator
    ): Response
    {
        $response = new JsonResponse();
        try {
            if ($request->request->get('ad_uuid') && ($ad = $this->adRepository->findOneBy(['ad_uuid' => $request->request->get('ad_uuid')]))) {
                $serializer = new Serializer(
                    [new ObjectNormalizer()],
                    ['json' => new JsonEncoder()]
                );
                $car = $serializer->deserialize(
                    json_encode($request->request->all(), JSON_UNESCAPED_UNICODE),
                    Car::class,
                    'json'
                );
                $car->setAvatar($request->files->get('avatar'));
                $car->setId($ad->getCar()->getId());
                $errors = $validator->validate($car);
                if (! $errors->count()) {
                    $this->carRepository->update($car);
                    $filesystem = new Filesystem();
                    $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $ad->getCar()->getId();
                    $filesystem->remove($destination);
                    $car->getAvatar()->move($destination, 'avatar.jpeg');
                    $response->setData($ad->getAdParams());
                } else {
                    $message = [];
                    foreach ($errors as $error) {
                        if (!isset($message[$error->getPropertyPath()])) {
                            $message[$error->getPropertyPath()] = $error->getMessage();
                        }
                    }
                    $response->setData(['error' => $message]);
                    $response->setStatusCode(422);
                }
            } else {
                $response->setData(['error' => ['Данного объявления не существует. Обновите страницу']]);
                $response->setStatusCode(422);
            }
       } catch (Exception $exception) {
            $response->setData(['error' => ['Сервис недоступен. Попробуйте попытку позже']]);
            $response->setStatusCode(503);
        }
        return $response;
    }

    /**
     * @Route("/ad", name="ad_delete", methods={"DELETE"})
     */
    public function deleteAd(
        Request $request
    ): Response
    {
        $response = new JsonResponse();
        try {
            if ($request->request->get('ad_uuid') && ($ad = $this->adRepository->findOneBy(['ad_uuid' => $request->request->get('ad_uuid')]))) {
                $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $ad->getCar()->getId();
                $this->carRepository->deleteWithAd($ad);
                $filesystem = new Filesystem();
                $filesystem->remove($destination);
                $response->setData($ad->getAdUuid());
            } else {
                $response->setData(['error' => ['Данного объявления не существует. Обновите страницу']]);
                $response->setStatusCode(422);
            }
        } catch (Exception $exception) {
            $response->setData(['error' => ['Сервис недоступен. Попробуйте попытку позже']]);
            $response->setStatusCode(503);
        }
        return $response;
    }
}
