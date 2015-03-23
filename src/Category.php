<?php
class Category {
  private $name;
  private $id;

  function __construct($name, $id = null) {
    $this->name = $name;
    $this->id = $id;
  }

  // setters
  function setName ($name) {
    $this->name = (string) $name;
  }

  function setId($id) {
    $this->id = (int) $id;
  }

  // getters
  function getName() {
    return $this->name;
  }

  function getId() {
    return $this->id;
  }

  // dB

  function save() {
    $statement = $GLOBALS['DB']->query("INSERT INTO categories (name) VALUES ('{$this->getName()}') RETURNING id;");
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $this->setId($result['id']);
  }

  function update($new_name) {
    $GLOBALS['DB']->exec("UPDATE categories SET name = '{$new_name}' WHERE id = {$this->getId()}");
    $this->setName($new_name);
  }

  function delete() {
    $GLOBALS['DB']->exec("DELETE FROM categories WHERE id = {$this->getId()};");
    $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE category_id = {$this->getId()};");
  }

  function addTask($task) {
    $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$this->getId()}, {$task->getId()});");
  }

  function getTasks() {
    $query = $GLOBALS['DB']->query("SELECT task_id FROM categories_tasks WHERE category_id = {$this->getId()};");
    $task_ids = $query->fetchAll(PDO::FETCH_ASSOC);

    $tasks = [];
    foreach ($task_ids as $id) {
      $task_id = $id['task_id'];
      $result = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE id = {$task_id};");
      $returned_task = $result->fetchAll(PDO::FETCH_ASSOC);

      $description = $returned_task[0]['description'];
      $id = $returned_task[0]['id'];
      $due_date = $returned_task[0]['due_date'];
      $due_date = str_replace("-", "/", $due_date);
      $new_task = new Task($description, $id, $due_date);
      array_push($tasks, $new_task);
    }
    return $tasks;
  }

  static function getAll() {
    $returned_categories = $GLOBALS['DB']->query("SELECT * FROM categories;");
    $categories = [];
    foreach ($returned_categories as $category) {
      $name = $category['name'];
      $id = $category['id'];
      $new_category = new Category($name, $id);
      array_push($categories, $new_category);
    }
    return $categories;
  }

  // function getTasks() {
  //   $tasks = [];
  //   $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE category_id = {$this->getId()} ORDER BY due_date ASC;");
  //   foreach ($returned_tasks as $task) {
  //     $due_date = $task['due_date'];
  //     $due_date = str_replace("-", "/", $due_date);
  //     $new_Task = new Task($task['description'], $task['id'], $due_date);
  //     array_push($tasks, $new_Task);
  //   }
  //   return $tasks;
  // }

  static function deleteAll() {
    $GLOBALS['DB']->exec("DELETE FROM categories *;");
  }

  static function find($search_id) {
    $found_category = null;
    $categories = Category::getAll();
    foreach($categories as $category) {
      $category_id = $category->getId();
      if($category_id == $search_id) {
        $found_category = $category;
      }
    }
    return $found_category;
  }

  static function search($description) {
    $tasks = [];
    $returned_tasks = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE description = '{$description}';");
    foreach ($returned_tasks as $task) {
      $due_date = $task['due_date'];
      $due_date = str_replace("-", "/", $due_date);
      $new_Task = new Task($task['description'], $task['id'], $due_date);
      array_push($tasks, $new_Task);
    }
    return $tasks;
  }
}
