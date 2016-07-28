<?php

namespace Fonsecas72\GiffyExtension\Driver;

use Behat\Mink\Driver\Selenium2Driver;

class GiffyDriver extends Selenium2Driver
{
    private $c = 0;
    private $i = 0;
    private $giffyShotsGlobalPath = '';
    private $giffyScenarioShotsPath = '';

    public function __construct($path, $browserName, $desiredCapabilities, $wdHost)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $this->giffyShotsGlobalPath = $path;

        parent::__construct($browserName, $desiredCapabilities, $wdHost);
    }

    public function giffy()
    {
        $frames = [];
        $durations = [];
        for ($index = 0; $index < $this->c; $index++) {
            if ($index === 0 || $index === 1 || $index === 2) {
                continue;
            }
            $frames[] = $this->getScreenshotDestination().DIRECTORY_SEPARATOR.$this->getSerializedName($this->i, $index);

            if ($this->c - 1 === $index) {
                $durations[] = 150;
                continue;
            }
            $durations[] = 30;
        }

        // Initialize and create the GIF !
        $gc = new \GifCreator\GifCreator();
        $gc->create($frames, $durations);
        $gifBinary = $gc->getGif();
        $gifFilename = $this->getScreenshotDestination().DIRECTORY_SEPARATOR.$this->giffyScenarioShotsPath.$this->i.'.gif';
        file_put_contents($gifFilename, $gifBinary);
        echo "| Gif captured ~> ".$gifFilename.PHP_EOL.PHP_EOL;

        foreach ($this->screenshotsTaked as $screenshot) {
            unlink($screenshot);
        }
    }

    public function resetCounter()
    {
        $this->c = 0;
        $this->i++;
        $this->screenshotsTaked = [];
    }
    public function setScenarioPath($path)
    {
        $this->giffyScenarioShotsPath = $path;
        if (!file_exists($this->getScreenshotDestination())) {
            mkdir($this->getScreenshotDestination(), 0777, true);
        }
    }

    private function getScreenshotDestination()
    {
        if ($this->giffyScenarioShotsPath === '') {
            return $this->giffyShotsGlobalPath;
        }
        return $this->giffyShotsGlobalPath.DIRECTORY_SEPARATOR.$this->giffyScenarioShotsPath;
    }
    private function getShotName()
    {
        return $this->getSerializedName($this->i, $this->c++);
    }
    private function getSerializedName($scenarioId, $stepId)
    {
        return 'shot_'.sprintf('%03d', $scenarioId).'_'.sprintf('%03d', $stepId).'.png';
    }

    private $screenshotsTaked = [];

    private function saveScreenshot()
    {
        $screenshotFilename = $this->getScreenshotDestination().DIRECTORY_SEPARATOR.$this->getShotName();
        file_put_contents($screenshotFilename, parent::getScreenshot());
        $this->screenshotsTaked[] = $screenshotFilename;
    }
    private function highlight($xpath)
    {
        $styleChanged = false;
        try {
            $oldStylesList = $this->getElementStyleArray($xpath);
            $this->addStylesToElement($xpath, [
                'outline' => '1px solid rgb(136, 255, 136)',
                'backgroundColor' => 'yellow',
            ]);
            $styleChanged = true;
        } catch (\Exception $exc) {
        }
        $this->saveScreenshot();
        if ($styleChanged === true) {
            try {
                $this->resetStyle($xpath, $oldStylesList);
            } catch (\Exception $exc) {
            }
        }
    }
    private function getElementStyleArray($xpath)
    {
        $currentStyles = parent::executeJsOnXpath($xpath, 'return {{ELEMENT}}.style', true);

        $oldStyles = [];
        foreach ($currentStyles as $value) {
            $oldStyles[$value] = parent::executeJsOnXpath($xpath, 'return {{ELEMENT}}.style.'.$value, true);
        }
        return $oldStyles;
    }

    private function resetStyle($xpath, $oldStyles)
    {
        parent::executeJsOnXpath($xpath, '{{ELEMENT}}.style = null;', true);
        $this->addStylesToElement($xpath, $oldStyles);
    }

    private function addStylesToElement($xpath, $styles)
    {
        foreach ($styles as $style => $value) {
            parent::executeJsOnXpath($xpath, '{{ELEMENT}}.style.'.$style.' = "'.$value.'";', true);
        }
    }

    // DO I REALLY HAVE TO DO THIS?

    public function visit($url)
    {
        $this->saveScreenshot();
        parent::visit($url);
        $this->saveScreenshot();
    }
    public function click($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::click($xpath);
        $this->saveScreenshot();
    }


    public function getText($xpath)
    {
        $this->highlight($xpath);
        return parent::getText($xpath);
    }
    public function getAttribute($xpath, $name)
    {
        $this->highlight($xpath);
        return parent::getAttribute($xpath, $name);
    }
    public function getValue($xpath)
    {
        $this->highlight($xpath);
        return parent::getValue($xpath);
    }


    public function setValue($xpath, $value)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::setValue($xpath, $value);
        $this->saveScreenshot();
    }
    public function check($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::check($xpath);
        $this->saveScreenshot();
    }
    public function uncheck($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::uncheck($xpath);
        $this->saveScreenshot();
    }
    public function selectOption($xpath, $value, $multiple = false)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::selectOption($xpath, $value, $multiple);
        $this->saveScreenshot();
    }
    public function attachFile($xpath, $path)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::attachFile($xpath, $path);
        $this->saveScreenshot();
    }
    public function resizeWindow($width, $height, $name = null)
    {
        $this->saveScreenshot();
        parent::resizeWindow($width, $height, $name);
        $this->saveScreenshot();
    }
    public function back()
    {
        $this->saveScreenshot();
        parent::back();
        $this->saveScreenshot();
    }
    public function blur($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::blur($xpath);
        $this->saveScreenshot();
    }
    public function doubleClick($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::doubleClick($xpath);
        $this->saveScreenshot();
    }
    public function dragTo($sourceXpath, $destinationXpath)
    {
        $this->saveScreenshot();
        parent::dragTo($sourceXpath, $destinationXpath);
        $this->saveScreenshot();
    }
    public function evaluateScript($script)
    {
        $this->saveScreenshot();
        parent::evaluateScript($script);
        $this->saveScreenshot();
    }
    public function executeScript($script)
    {
        $this->saveScreenshot();
        parent::executeScript($script);
        $this->saveScreenshot();
    }
    public function focus($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::focus($xpath);
        $this->saveScreenshot();
    }
    public function forward()
    {
        $this->saveScreenshot();
        parent::forward();
        $this->saveScreenshot();
    }
    public function keyDown($xpath, $char, $modifier = null)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::keyDown($xpath, $char, $modifier);
        $this->saveScreenshot();
    }
    public function keyPress($xpath, $char, $modifier = null)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::keyPress($xpath, $char, $modifier);
        $this->saveScreenshot();
    }
    public function keyUp($xpath, $char, $modifier = null)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::keyUp($xpath, $char, $modifier);
        $this->saveScreenshot();
    }
    public function maximizeWindow($name = null)
    {
        $this->saveScreenshot();
        parent::maximizeWindow($name);
        $this->saveScreenshot();
    }
    public function mouseOver($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::mouseOver($xpath);
        $this->saveScreenshot();
    }
    public function reload()
    {
        $this->saveScreenshot();
        parent::reload();
        $this->saveScreenshot();
    }
    public function rightClick($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::rightClick($xpath);
        $this->saveScreenshot();
    }
    public function submitForm($xpath)
    {
        $this->saveScreenshot();
        $this->highlight($xpath);
        parent::submitForm($xpath);
        $this->saveScreenshot();
    }
    public function switchToIFrame($name = null)
    {
        $this->saveScreenshot();
        parent::switchToIFrame($name);
        $this->saveScreenshot();
    }
    public function switchToWindow($name = null)
    {
        $this->saveScreenshot();
        parent::switchToWindow($name);
        $this->saveScreenshot();
    }
}
