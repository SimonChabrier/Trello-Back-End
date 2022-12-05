<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Column;
use App\Entity\User;
use Faker\Factory as Faker;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{   
    private $connexion;

    public function __construct(Connection $connexion)
    {
        $this->connexion = $connexion;
    }

    private function truncate ()
    {

        $this->connexion->executeQuery('SET foreign_key_checks = 0');
        $this->connexion->executeQuery('TRUNCATE TABLE `task`');
        $this->connexion->executeQuery('TRUNCATE TABLE `column`');
        $this->connexion->executeQuery('TRUNCATE TABLE `user`');



    }

    public function load(ObjectManager $manager): void
    {
        $this->truncate();

        $faker = Faker::create('fr_FR');

        $users = [];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setEmail('user' . $i . '@user.fr');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']);

            $users[] = $user;

            $manager->persist($user);
        };

        $columns = [];
        for ($i = 0; $i < 4; $i++) {
            $column = new Column();
            $column->setColumnName('column ' . $i);
            $column->setColumnNumber($i);

            $columns[] = $column;

            $manager->persist($column);
        };

        //$tasks = [];
        $colors = [ 'card--color--orange', 'card--color--blue', 'card--color--red', 'card--color--default'];
        $text_area = [ '100', '150', '200'];

        for ($i = 0; $i < 3; $i++) {
            $task = new Task();
            $task->setTaskTitle($faker->sentence(3));
            $task->setTaskContent($faker->paragraph(3));
            $task->setColumnNumber($faker->numberBetween(1, 4));
            
             //TODO gérer les numéros de cate par colonne
            $task->setCardNumber($faker->numberBetween(1, 4));
            
            $task->setCardColor($colors[mt_rand(0, 3)]);
            $task->setTextareaHeight($text_area[mt_rand(0, 2)]);
            $taskStatus = ['false', 'true'];
            $task->setTaskDone(array_rand($taskStatus , 1));
            
            for ($j = 0; $j < 3; $j++) {
                $task->addUser($users[mt_rand(0, 9)]);
            }
            
            for ($k = 0; $k < 3; $k++) {
                $task->setTaskColumn($columns[mt_rand(0, 3)]);
            }

            $tasks[] = $task;

            foreach ($tasks as $task) {
                $colNumber = $task->getColumnNumber();
                $task->setTaskColumn($columns[$colNumber - 1]);
            }

            $manager->persist($task);
        };

        $manager->flush();
    }
}
