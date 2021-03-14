<?php

declare(strict_types = 1);
namespace Fantestic\CestManager\ActionProvider;

use App\CestManager\Contract\ActionProviderInterface;
use IteratorAggregate;

/**
 * Facade to access all DataProviders.
 * 
 * @package Fantestic/ApiPlatform
 * @author Gerald Baumeister <gerald@fantestic.io>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class ActionProviderCollection
{
    /**
     * @var ActionProviderInterface[]
     */
    private IteratorAggregate $actionProviders;


    public function __construct(IteratorAggregate $actionProviders)
    {
        $this->actionProviders = $actionProviders;
    }


    /**
     * 
     * @return iterator|ActionProviderInterface[]
     */
    public function getActionProviders() :iterable
    {
        return $this->actionProviders;
    }
}
