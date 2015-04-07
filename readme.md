## Install

just add to your composer.json

~~~
"alfred-nutile-inc/behat-base-install": "dev-master"
~~~

It will pull in all your behat, setup your folders and behat.yml file as well

You will have a folder layout after this of

~~~
tests/acceptance
~~~

In here you will have `bootstrap` folder.

This will have the FeatureContext file for you to override many features that will not get overwritten the next time you update the library.

You can also remove traits you do not want


You will also have the `features` folder in there are many example tests.

Running `bin/behat` will show you all of those working once you have Selenium setup (see notes below on that)

We made this to work with a standard PHP library where vendor folder is at the root of the app.

If your site is in another folder then add to your .env file `TEST_ROOT`

For example if you put `/home/vagrant/Code/foo` then the base tests folder will be be `/home/vagrant/Code/foo/tests`

## Example Tests to Show How Things Work

All of these are in the `tests/acceptance/features` folder.

Run them all

~~~
bin/behat --tags=~@wip
~~~

@wip are test we use but will not work with this base install outside of our Laravel apps we have made
since they depend on that db etc. But are here to show how we use them.


### example_faker.feature

Shows you how you can make random emails, username etc to fill in forms.
The fields you see are the only ones available right now. But we are using the PHP Faker
library https://github.com/fzaninotto/Faker so more is possible.

### example_token.feature

Shows you how you can use tokens to keep tests clean and reusable. The location and naming of files are key.

#### Naming and Location
File is the name of the tests + default.token or 123445.token. So test foo.feature would have foo.feature.default.token

It is in the tokens folder right above the test file.

#### Default prefix
foo.feature.default.token is our future plan to include *.default.token file even if not chosen and then merge it to the chosen tokens. This way you can have a base token set that can be extended by another token sets.

### example_mockery.feature

This is for Laravel installs right now but with this we can mock data on the fly. For
drupal sites the drupal api might be more help.

Note that the FAKER_EMAIL, FAKER_UUID, FAKER etc can be used with this.

### example_api_testing.feature

This is key for all our apis. Our example is a working example against one of our apps.
Note the `TOKEN_REPLACE` this is part of our oauth work. The `Given I make an access token`
makes tokens on the fly and we later use them to run the test.

See more about that in `example_oauth.feature`

### example_oauth.feature

This test will show how you can test system that use oauth and how to also use the .env file.
For example `ADMIN_PASS` can be pulled from this file and in BehatEditor it is part of your ENV settings.

### example_drupal_remote_api.feature

The remote API driver extends the popular [Drupal Extention](https://github.com/jhedstrom/drupalextension) library to support
running authenticated Behat tests against remote Drupal sites. Be sure to set the remote site credentials in the behat.yml file.

## Including your custom steps

The format here is that custom tests are grabbed from the git repo and branch they live on. So to work
locally just add your custom step to the tests/acceptance/custom folder and they will be pulled in locally on the fly.

But later you need to make a fork of the central repo, add your steps there and we will approve it.

@TODO more info here

@TODO how to put this in .gitignore but make a realistic workflow for teams.
  composer.json entry ?

## BaseURL Inside Versus Outside VM

## UI Testing Javascript Use @javascript

## Non Javascript pages leave out @javascript 

## Selenium Install and Chrome

### Hosts file

`selenium-server.dev` is the default host so just set your host file for what every system
 you are running in, vagrant, Windows etc to be the right ip to this domain.
 As teams this makes it easier to work together.

## Installing WebDriver and using it

## What is the .env file

Setting APP_ENV and other settings

Passwords etc can be used later in your tests


### Chrome

## Adding your own steps outside of the repo
