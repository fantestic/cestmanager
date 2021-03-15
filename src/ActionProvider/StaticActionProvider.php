<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\ActionProvider;

use Fantestic\CestManager\Contract\ActionProviderInterface;


/**
 * Generates a list of all ActionProviders using the Symfony ContainerBuilder.
 * 
 * @see App\Kernel::build()
 * @package Fantestic/ApiPlatform
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class StaticActionProvider implements ActionProviderInterface
{

    public function getActions() :iterable {
        $actions = [
            [
                'see',
                'I see $text',
                [['name' => '$text', 'providers' => []]]
            ],
            [
                'dontSee',
                'I dont see $text',
                [['name' => '$text', 'providers' => []]]
            ],
            [
                'amOnPage',
                'I am on page $page',
                [['name' => '$page', 'providers' => []]]
            ],
            [
                'fillField',
                'I fill field $field with value $value',
                [
                    ['name' => '$field', 'providers' => []],
                    ['name' => '$value', 'providers' => []],
                ]
            ],
            [
                'selectOption',
                'I select option $option from dropdwon $dropdown',
                [
                    ['name' => '$option', 'providers' => []],
                    ['name' => '$dropdown', 'providers' => []],
                ]
            ],
        ];

        foreach ($actions as $action) {
            yield $this->createAction($action);
        }
    }


    private function createAction(array $conf) :Action
    {
        return new Action(
            Id::fromString($conf[0]),
            $conf[1],
            $conf[2]
        );
    }
}
