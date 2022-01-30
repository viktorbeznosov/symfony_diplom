<?php

namespace App\DataFixtures;

use App\Entity\Subscribe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscribeFixture extends Fixture
{

    const FEATURES = array(
        'more_that_one_article' => 'Возможность создать более 1 статьи',
        'basic_generator_features' => 'Базовые возможности генератора',
        'advanced_generator_features' => 'Продвинутые возможности генератора',
        'own_modules' => 'Свои модули',
        'unlimited_article_generation' => 'Безлимитная генерация статей для вашего аккаунта'
    );

    public function load(ObjectManager $manager): void
    {
        $free = new Subscribe();
        $features = json_encode(array(
            array(
                'title' => self::FEATURES['more_that_one_article'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['basic_generator_features'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['advanced_generator_features'],
                'enable' => false
            ),
            array(
                'title' => self::FEATURES['own_modules'],
                'enable' => false
            ),
        ), JSON_UNESCAPED_UNICODE);

        $free
            ->setTitle('Свободная подписка')
            ->setCode('free')
            ->setCost(0)
            ->setFeatures($features)
        ;
        $manager->persist($free);

        $plus = new Subscribe();
        $features = json_encode(array(
            array(
                'title' => self::FEATURES['more_that_one_article'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['basic_generator_features'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['advanced_generator_features'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['own_modules'],
                'enable' => false
            ),
        ), JSON_UNESCAPED_UNICODE);

        $plus
            ->setTitle('Продвинутая подписка')
            ->setCode('plus')
            ->setCost(9)
            ->setFeatures($features)
        ;

        $manager->persist($plus);

        $pro = new Subscribe();
        $features = json_encode(array(
            array(
                'title' => self::FEATURES['unlimited_article_generation'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['basic_generator_features'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['advanced_generator_features'],
                'enable' => true
            ),
            array(
                'title' => self::FEATURES['own_modules'],
                'enable' => true
            ),
        ), JSON_UNESCAPED_UNICODE);

        $pro
            ->setTitle('Премиум подписка')
            ->setCode('pro')
            ->setCost(49)
            ->setFeatures($features)
        ;

        $manager->persist($pro);

        $manager->flush();
    }
}
