<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Category.php";
  require_once "src/Task.php";

  $DB = new PDO('pgsql:host=localhost;dbname=to_do_test;password=password');

  class CategoryTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Task::deleteAll();
      Category::deleteAll();
    }

    function test_getName() {
      // Arrange
      $name = "work stuff";
      $test_Category = new Category($name);

      // Act
      $result = $test_Category->getName();

      // Assert
      $this->assertEquals($name, $result);
    }

    function test_getId() {
      // Arrange
      $name = "Work stuff";
      $id = 1;
      $test_Category = new Category($name, $id);

      // Act
      $result = $test_Category->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Assert
      $name = "Work stuff";
      $test_Category = new Category($name);

      // Act
      $test_Category->setId(2);
      $result = $test_Category->getId();

      // Assert
      $this->assertEquals(2, $result);
    }

    function test_save() {
      // Arrange
      $name = "Work stuff";
      $test_Category = new Category($name);
      $test_Category->save();

      // Act
      $result = Category::getAll();

      // Assert
      $this->assertEquals($test_Category, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $name = "Work stuff";
      $name2 = "Home stuff";
      $test_Category = new Category($name);
      $test_Category->save();
      $test_Category2 = new Category($name2);
      $test_Category2->save();

      // Act
      $result = Category::getAll();

      // Assert
      $this->assertEquals([$test_Category, $test_Category2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $name = "Wash the dog";
      $name2 = "Home stuff";
      $test_Category = new Category($name);
      $test_Category->save();
      $test_Category2 = new Category($name);
      $test_Category2->save();

      // Act
      Task::deleteAll();
      Category::deleteAll();
      $result = Category::getAll();

      // Assert
      $this->assertEquals([], $result);
    }

    function test_find() {
      // Arrange
      $name = "Wash the dog";
      $name2 = "Home stuff";
      $test_Category = new Category($name);
      $test_Category->save();
      $test_Category2 = new Category($name2);
      $test_Category2->save();

      // Act
      $result = Category::find($test_Category->getId());

      // Assert
      $this->assertEquals($test_Category, $result);
    }

    function test_getTasks() {
      // Arrange
      $name = "work stuff";
      $test_Category = new Category($name);
      $test_Category->save();

      $test_category_id = $test_Category->getId();
      $description = "email client";
      $test_task = new Task($description, $test_category_id, 1, '1999/01/01');
      $test_task->save();

      $description2 = "meet with biscuit head";
      $test_task2 = new Task($description2, $test_category_id, 2, '2000/01/01');
      $test_task2->save();

      // Act
      $result = $test_Category->getTasks();

      // Assert
      $this->assertEquals([$test_task, $test_task2], $result);
    }

    function test_search() {
      // Arrange
      $name = "work stuff";
      $test_Category = new Category($name);
      $test_Category->save();

      $test_category_id = $test_Category->getId();
      $description = "email client";
      $test_task = new Task($description, $test_category_id);
      $test_task->save();

      // Act
      $result = $test_Category->search($description);

      // Assert
      $this->assertEquals($test_task, $result[0]);
    }

    function test_update() {
      // Assert
      $name = "Work stuff";
      $id = null;
      $test_category = new Category($name, $id);
      $test_category->save();

      $new_name = "Home stuff";

      // Act
      $test_category->update($new_name);

      // Assert
      $this->assertEquals("Home stuff", $test_category->getName());
    }

    function testDelete() {
      // Arrange
      $name = "Work stuff";
      $test_category = new Category($name);
      $test_category->save();

      $name2 = "Home stuff";
      $test_category2 = new Category($name2);
      $test_category2->save();

      // Act
      $test_category->delete();

      // Assert
      $this->assertEquals([$test_category2], Category::getAll());
    }

    function testDeleteCategoryTasks() {
      // Arrange
      $name = "Work stuff";
      $test_category = new Category($name);
      $test_category->save();

      $description = "Build website";
      $category_id = $test_category->getId();
      $test_task = new Task($description, $category_id);
      $test_task->save();

      // Act
      $test_category->delete();

      // Assert
      $this->assertEquals([], Task::getAll());
    }
  }
?>
