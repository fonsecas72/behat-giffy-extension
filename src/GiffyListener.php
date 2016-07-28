<?php

namespace Fonsecas72\GiffyExtension;

use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Behat\Mink\Mink;
use Behat\Behat\EventDispatcher\Event\ScenarioLikeTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\AfterOutlineTested;
use Behat\Behat\EventDispatcher\Event\BeforeOutlineTested;
use Behat\Behat\EventDispatcher\Event\BeforeScenarioTested;

class GiffyListener implements EventSubscriberInterface
{
    const GIFFY_TAG = 'giffy';

    /** @var Mink  */
    private $mink;
    private $defaultSessionName;
    private $useScenarioFolder;
    private $path = '';
    private $giffyEnabled = false;
    
    public function __construct(Mink $mink, $useScenarioFolder = false)
    {
        $this->mink = $mink;
        $this->useScenarioFolder = $useScenarioFolder;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::BEFORE => array(
                array('enableGiffy'),
                array('saveDefaultSession'),
            ),
            ScenarioTested::AFTER  => array(
                array('giffy'),
                array('restoreDefaultSession'),
            ),

            // events for outlines / examples
            OutlineTested::BEFORE => array(
                array('setDestinationFolderWhenOutline'), // i'm setting the destination folder to be the outline title
            ),
            ExampleTested::BEFORE => array(
                array('enableGiffy'),
                array('saveDefaultSession'),
            ),
            ExampleTested::AFTER   => array(
                array('giffy'),
            ),
            OutlineTested::AFTER => array(
                array('restoreDefaultSession'),
            ),
        );
    }

    public function saveDefaultSession()
    {
        if ($this->giffyEnabled) {
            $this->defaultSessionName = $this->mink->getDefaultSessionName();
        }
    }
    public function setDestinationFolderWhenOutline($event)
    {
        $this->path = str_replace(' ', '_', $event->getOutline()->getTitle());
    }
    public function enableGiffy($event)
    {
        if ($this->hasGiffyTag($event) || $this->mink->getDefaultSessionName() === static::GIFFY_TAG) {
            $this->giffyEnabled = true;
            $this->mink->setDefaultSessionName('giffy');
            $this->mink->getSession()->getDriver()->resetCounter();
            if ($this->useScenarioFolder) {

                if ('Scenario' === $event->getNode()->getNodeType()) {
                    $this->path = str_replace(' ', '_', $event->getScenario()->getTitle());
                } 

                $this->mink->getSession()->getDriver()->setScenarioPath($this->path);
            }
        }
    }
    public function giffy()
    {
        if ($this->giffyEnabled) {
            $this->mink->getSession()->getDriver()->giffy();
        }
    }
    public function restoreDefaultSession()
    {
        if ($this->giffyEnabled) {
            $this->mink->setDefaultSessionName($this->defaultSessionName);
            $this->giffyEnabled = false;
            $this->path = '';
        }
    }
    private function hasGiffyTag($event)
    {
        if (method_exists($event, 'getOutline')) {
            return $event->getOutline()->hasTag(self::GIFFY_TAG) || $event->getFeature()->hasTag(self::GIFFY_TAG);
        }
        
        return $event->getScenario()->hasTag(self::GIFFY_TAG) || $event->getFeature()->hasTag(self::GIFFY_TAG);
    }
}
