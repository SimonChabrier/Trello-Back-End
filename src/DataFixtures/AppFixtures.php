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
            $user->setPassword('password');

            $users[] = $user;

            $manager->persist($user);
            //TODO assigner des tasks à chaque user
        };

        $columns = [];
        for ($i = 0; $i < 5; $i++) {
            $column = new Column();
            $column->setColumnName('column' . $i);
            $column->setColumnNumber($i);

            $columns[] = $column;

            $manager->persist($column);
            //TODO assigner des tasks à chaque column
        };

        $tasks = [];
        $colors = [
            'card--color--orange',
            'card--color--blue',
            'card--color--red',
            'card--color--default',
        ];
        $text_area = [
            '100',
            '150',
            '200',
        ];
        for ($i = 0; $i < 30; $i++) {
            $task = new Task();
            $task->setTaskTitle($faker->sentence(3));
            $task->setTaskContent($faker->paragraph(3));
            $task->setColumnNumber(mt_rand(1, 5));
            $task->setCardNumber('1');
            $task->setCardColor($colors[mt_rand(0, 3)]);
            $task->setTextareaHeight($text_area[mt_rand(0, 2)]);
            $task->setTaskDone(rand(0, 1));
            $task->addUser($users[mt_rand(0, 9)]);
            $task->setTaskColumn($columns[mt_rand(0, 4)]);

            $tasks[] = $task;

            $manager->persist($task);
        };

        $manager->flush();
    }
}
