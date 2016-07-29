# Giffy - behat-giffy-extension

Behat extension that generates an animated gif with the complete interaction of tagged scenarios

This extension adds a driver that will allow you  to create a animated GIF based on saved screenshots for each interaction with the browser.

Enable it by adding it to your session group and by adding a `@giffy` tag to your feature/scenario.

You can also use as your default session (javascript maybe) but be aware that it will slow down your tests.

**It works on top of selenium2**

##Example:

```
default:
    extensions:
        Fonsecas72\GiffyExtension:
            screenshot_path: build/gifs
            use_scenario_folder: true
        Behat\MinkExtension:
            base_url:             http://link.php
            files_path:           'features'
            browser_name:         firefox
            default_session:      selenium2
            javascript_session:   selenium2
            sessions:
                selenium2:
                    selenium2: ~
                giffy:
                    giffy: ~
giffy:
    extensions:
            Behat\MinkExtension:
                default_session:      giffy
                javascript_session:   giffy
```

Then you could do:

`behat -p giffy`

Or you can append the `@giffy` tag to your feature/scenario.
