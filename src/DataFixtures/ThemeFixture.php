<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixture extends Fixture
{
    private $thenes = [
        [
            'title' => 'Тестовая статья',
            'code' => 'test',
            'content' => '<?xml version="1.0" encoding="UTF-8" ?>
                            <root>
                                <block>
                                    <header>Добавляйте свои слова</header>
                                    <paragraph>При генерации контента статьи, вы можете наполнить его нужными словами для вашего бизнеса. Столько сколько нужно. Хоть все ими заполоните!</paragraph>
                                </block>
                                <block>
                                    <header>Вставляйте изображения</header>
                                    <block>
                                        <paragraph>Надоели стандартные красивые изображения. Прикрепляйте к вашим статьям свою уникальные фотографии. Смазанные, с пальцем на пол фотографии, с кривым лицом. Все пойдет - вы здесь главный!</paragraph>
                                    </block>
                                    <image>uploads/images/themes/bg-showcase-2.jpg</image>
                                </block>
                                <block>
                                    <header>Интегрируйтесь по API</header>
                                    <paragraph>Придумайте и настройте свою собственную интеграцию с сервисом. Нужно ответить на комментарий в соц.сети - получите его по API. Нужно написать новую статью по программированию - получите ее по API. Хотите вкусно покушать - сходите за едой, а статью пускай за вас напишет API!</paragraph>
                                </block>
                            </root>',
        ],
        [
            'title' => 'По PHP',
            'code' => 'php',
            'content' => '<?xml version="1.0" encoding="UTF-8" ?>
                <root>
                    <block>
                        <header>Lorem ipsum</header>
                        <paragraph>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </paragraph>
                        <paragraph>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </paragraph>
                    </block>
                    <block>
                        <paragraph>
                            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </paragraph>
                        <image>uploads/images/themes/php2.jpg</image>
                    </block>
                    <block>
                        <paragraph>
                            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </paragraph>
                    </block>
                </root>
            ',
        ],
        [
            'title' => 'Про еду',
            'code' => 'food',
            'content' => '<?xml version="1.0" encoding="UTF-8" ?>
                    <root>
                        <block>
                            <header>Lorem ipsum</header>
                            <paragraph>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </paragraph>
                        </block>
                        <block>
                            <paragraph>
                                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                            </paragraph>
                            <image>uploads/images/themes/food1.jpg</image>
                        </block>
                        <block>
                            <image>uploads/images/themes/food2.jpg</image>
                            <paragraph>
                                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                            </paragraph>
                        </block>
                    </root>
            ',
        ],
        [
            'title' => 'Про животных',
            'code' => 'animals',
            'content' => '<?xml version="1.0" encoding="UTF-8" ?>
                            <root>
                                <block>
                                    <header>Lorem ipsum</header>
                                    <paragraph>
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                    </paragraph>
                                </block>
                                <block>
                                    <paragraph>
                                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                    </paragraph>
                                    <image>uploads/images/themes/animals1.jpg</image>
                                </block>
                                <block>
                                    <image>uploads/images/themes/animals2.jpg</image>
                                    <paragraph>
                                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                                    </paragraph>
                                </block>
                            </root>',
        ],
    ];

    public function load(ObjectManager $manager)
    {
        foreach ($this->thenes as $item) {
            $thene = new Theme();
            $thene->setTitle($item['title']);
            $thene->setCode($item['code']);
            $thene->setContent($item['content']);

            $manager->persist($thene);

            $manager->flush();
        }
    }
}
