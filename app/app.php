<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Task.php";
  require_once __DIR__."/../src/Category.php";

  $app = new Silex\Application();

  $DB = new PDO('pgsql:host=localhost;dbname=to_do;password=password');

  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
  ));

  use Symfony\Component\HttpFoundation\Request;
  Request::enableHttpMethodParameterOverride();

  $app->get("/", function() use ($app) {
    return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()));
  });

  $app->get("/categories/{id}", function($id) use ($app) {
    $category = Category::find($id);
    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
  });

  $app->get("/categories/{id}/edit", function($id) use ($app) {
     $category = Category::find($id);
     return $app['twig']->render('category_edit.html.twig', array('category' => $category));
  });

  $app->patch("/categories/{id}", function($id) use ($app) {
    $name = $_POST['name'];
    $category = Category::find($id);
    $category->update($name);
    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
  });

  $app->post("/categories", function() use ($app) {
    $category = new Category($_POST['name']);
    $category->save();
    return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()));
  });

  $app->post("/tasks", function() use ($app) {
    $task = new Task($_POST['description'], $_POST['category_id'], null, $_POST['due_date']);
    $task->save();
    $category = Category::find($_POST['category_id']);
    return $app['twig']->render('category.html.twig', array('category' => $category, 'tasks' => $category->getTasks()));
  });

  $app->post("/search", function() use ($app) {
    $results = Category::search($_POST['name']);
    $temp = [];
    foreach($results as $result) {
      $temp_category = Category::find($result->getCategoryId());
      $name = $temp_category->getName();
      $new_task = new Task($name, null, null, $result->getDueDate());
      array_push($temp, $new_task);
    }
    return $app['twig']->render('search_results.html.twig', array('results' => $temp, 'search_term' => $_POST['name']));
  });

  $app->post("/deleteTasks", function() use ($app) {
    Task::deleteAll();
    return $app['twig']->render('index.html.twig');
  });

  $app->post("/deleteCategories", function() use ($app) {
    Category::deleteAll();
    return $app['twig']->render('index.html.twig');
  });

  $app->delete("/categories/{id}", function($id) use ($app) {
    $category = Category::find($id);
    $category->delete();
    return $app['twig']->render('index.html.twig', array('categories' => Category::getAll()));
  });

  return $app;
?>
