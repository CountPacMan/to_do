<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Task.php";

  $DB = new PDO('pgsql:host=localhost;dbname=to_do_test; password=password');

  class TaskTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Task::deleteAll();
      Category::deleteAll();
    }

    function test_save() {
      // Arrange
      $description = "Wash the dog";
      $test_task = new Task($description, 1);

      // Act
      $test_task->save();

      // Assert
      $result = Task::getAll();
      $this->assertEquals($test_task, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $description = "Wash the dog";
      $description2 = "Water the lawn";
      $test_Task = new Task($description, 1);
      $test_Task->save();
      $test_Task2 = new Task($description2, 1);
      $test_Task2->save();

      // Act
      $result = Task::getAll();

      // Assert
      $this->assertEquals([$test_Task, $test_Task2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $description = "Wash the dog";
      $description2 = "Water the lawn";
      $test_Task = new Task($description, 1);
      $test_Task->save();
      $test_Task2 = new Task($description2, 1);
      $test_Task2->save();

      // Act
      Task::deleteAll();

      // Assert
      $result = Task::getAll();
      $this->assertEquals([], $result);
    }

    function test_getId() {
      // Arrange
      $description = "Wash the dog";
      $id = 1;
      $test_Task = new Task($description, 1, $id);

      // Act
      $result = $test_Task->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Arrange
      $description = "Wash the dog";
      $test_Task = new Task($description, 1);

      // Act
      $test_Task->setId(2);

      // Assert
      $result = $test_Task->getId();
      $this->assertEquals(2, $result);
    }

    function test_find() {
      // Arrange
      $description = "Wash the dog";
      $description2 = "Water the lawn";
      $test_Task = new Task($description, 1, 1);
      $test_Task->save();
      $test_Task2 = new Task($description2, 1, 1);
      $test_Task2->save();

      // Act
      $result = Task::find($test_Task->getId());

      // Assert
      $this->assertEquals($test_Task, $result);
    }

    function test_dueDate() {
      // Arrange
      $description = "Wash the dog";
      $due_date = '1/18/1999';
      $test_Task = new Task($description, 1, 1, $due_date);

      // Act
      $result = $test_Task->getDueDate();

      // Assert

      $this->assertEquals($due_date, $result);
    }
  }
?>
