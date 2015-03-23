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
      $test_category = new Category($name);

      // Act
      $result = $test_category->getName();

      // Assert
      $this->assertEquals($name, $result);
    }

    function test_getId() {
      // Arrange
      $name = "Work stuff";
      $id = 1;
      $test_category = new Category($name, $id);

      // Act
      $result = $test_category->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Assert
      $name = "Work stuff";
      $test_category = new Category($name);

      // Act
      $test_category->setId(2);
      $result = $test_category->getId();

      // Assert
      $this->assertEquals(2, $result);
    }

    function test_save() {
      // Arrange
      $name = "Work stuff";
      $test_category = new Category($name);
      $test_category->save();

      // Act
      $result = Category::getAll();

      // Assert
      $this->assertEquals($test_category, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $name = "Work stuff";
      $name2 = "Home stuff";
      $test_category = new Category($name);
      $test_category->save();
      $test_category2 = new Category($name2);
      $test_category2->save();

      // Act
      $result = Category::getAll();

      // Assert
      $this->assertEquals([$test_category, $test_category2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $name = "Wash the dog";
      $name2 = "Home stuff";
      $test_category = new Category($name);
      $test_category->save();
      $test_category2 = new Category($name);
      $test_category2->save();

      // Act
      Task::deleteAll();
      Category::deleteAll();
      $result = Category::getAll();

      // Assert
      $this->assertEquals([], $result);
    }

    function testDelete() {
      //Arrange
      $name = "Work stuff";
      $id = 1;
      $test_category = new Category($name, $id);
      $test_category->save();

      $description = "File reports";
      $id2 = 2;
      $test_task = new Task($description, $id2);
      $test_task->save();

      //Act
      $test_category->addTask($test_task);
      $test_category->delete();

      //Assert
      $this->assertEquals([], $test_task->getCategories());
    }

    function test_find() {
      // Arrange
      $name = "Wash the dog";
      $name2 = "Home stuff";
      $test_category = new Category($name);
      $test_category->save();
      $test_category2 = new Category($name2);
      $test_category2->save();

      // Act
      $result = Category::find($test_category->getId());

      // Assert
      $this->assertEquals($test_category, $result);
    }

    function testAddTask() {
        //Arrange
        $name = "work stuff";
        $id = 1;
        $test_category = new Category($name, $id);
        $test_category->save();

        $description = "File reports";
        $id2 = 2;
        $test_task = new Task($description, $id2);
        $test_task->save();

        //Act
        $test_category->addTask($test_task);

        //Assert
        $this->assertEquals($test_category->getTasks()[0], $test_task);
    }

    function test_getTasks() {
      // Arrange
      $name = "work stuff";
      $id = 1;
      $test_category = new Category($name, $id);
      $test_category->save();

      $description = "email client";
      $id2 = 2;
      $test_task = new Task($description, $id2, '1999/01/01');
      $test_task->save();

      $description2 = "meet with biscuit head";
      $id3 = 3;
      $test_task2 = new Task($description2, $id3, '2000/01/01');
      $test_task2->save();

      // Act
      $test_category->addTask($test_task);
      $test_category->addTask($test_task2);

      // Assert
      $this->assertEquals($test_category->getTasks(), [$test_task, $test_task2]);
    }

    function test_search() {
      // Arrange
      $name = "work stuff";
      $test_category = new Category($name);
      $test_category->save();

      $test_category_id = $test_category->getId();
      $description = "email client";
      $test_task = new Task($description);
      $test_task->save();

      // Act
      $result = $test_category->search($description);

      // Assert
      $this->assertEquals($test_task, $result[0]);
    }

    function test_update() {
      // Assert
      $name = "Work stuff";
      $id = null;
      $test_category = new Category($name);
      $test_category->save();

      $new_name = "Home stuff";

      // Act
      $test_category->update($new_name);

      // Assert
      $this->assertEquals("Home stuff", $test_category->getName());
    }

    // function testDelete() {
    //   // Arrange
    //   $name = "Work stuff";
    //   $test_category = new Category($name);
    //   $test_category->save();
    //
    //   $name2 = "Home stuff";
    //   $test_category2 = new Category($name2);
    //   $test_category2->save();
    //
    //   // Act
    //   $test_category->delete();
    //
    //   // Assert
    //   $this->assertEquals([$test_category2], Category::getAll());
    // }

    // function testDeleteCategoryTasks() {
    //   // Arrange
    //   $name = "Work stuff";
    //   $test_category = new Category($name);
    //   $test_category->save();
    //
    //   $description = "Build website";
    //   $category_id = $test_category->getId();
    //   $test_task = new Task($description);
    //   $test_task->save();
    //
    //   // Act
    //   $test_category->delete();
    //
    //   // Assert
    //   $this->assertEquals([], Task::getAll());
    // }
  }
?>
