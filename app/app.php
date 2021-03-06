<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Car.php";

    session_start();

        if (empty($_SESSION['list_of_cars'])) {
                $_SESSION['list_of_cars'] = array();
            }

    $app = new Silex\Application();

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
    ));

    // Home: Submit Car Form Page
    $app->get("/", function() use ($app) {
        return $app['twig']->render('cars.html.twig', array('cars' => Car::getAll()));
    });

    // Single Car Create Page
    $app->post("/cars", function() use ($app) {
          $cars = new Car($_POST['image'], $_POST['make'], $_POST['price'], $_POST['miles'], $_POST['status']);
          $cars->save();
          return $app['twig']->render('cars.html.twig', array('cars' => Car::getAll(), 'newcar' => $cars));
    });

    // Searching by Price
    $app->get("/car_matches", function() use ($app){
        $cars = Car::getAll();
        $cars_matching_search = array();
        foreach ($cars as $car) {
            if ($car->worthBuying(($_GET["matchPrice"]))) {
                array_push($cars_matching_search, $car);
            }
        }
        return $app['twig']->render('cars.html.twig', array('cars' => $cars, 'match_cars' => $cars_matching_search));
    });

    // Searching by Make/Model
    $app->get("/make_model_matches", function() use ($app){
        $cars = Car::getAll();
        $cars_matching_make = array();
        foreach ($cars as $car) {
            if ($car->matchedMake($_GET['matchMake'])) {
                array_push($cars_matching_make, $car);
            }
        }
        return $app['twig']->render('cars.html.twig', array('cars' => $cars, 'match_models' => $cars_matching_make));
    });


    // Delete Cars Page
    $app->post("/delete_cars", function() use ($app) {
        Car::deleteAll();
        return $app['twig']->render('cars.html.twig');
    });

    return $app;
?>
