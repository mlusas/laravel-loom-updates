# Contributing to Laravel Loom Updates

First off, thank you for considering contributing to Laravel Loom Updates. It's people like you that make Laravel Loom Updates such a great tool.

## Where do I go from here?

If you've noticed a bug or have a feature request, make sure to check our [Issues](https://github.com/mlusas/laravel-loom-updates/issues) page to see if someone else in the community has already created a ticket. If not, go ahead and [make one](https://github.com/mlusas/laravel-loom-updates/issues/new)!

## Fork & create a branch

If this is something you think you can fix, then [fork Laravel Loom Updates](https://help.github.com/articles/fork-a-repo) and create a branch with a descriptive name.

A good branch name would be (where issue #325 is the ticket you're working on):

```sh
git checkout -b 325-add-delete-functionality
```

## Get the test suite running

Make sure you're using the latest version of PHP and Composer. Then, install the dependencies and run the tests:

```sh
composer install
composer test
```

## Implement your fix or feature

At this point, you're ready to make your changes! Feel free to ask for help; everyone is a beginner at first.

## Code Style

We use Laravel's coding style, which follows the PSR-2 coding standard and the PSR-4 autoloading standard. 

You can use PHP CS Fixer to format your code. Run the following command before submitting your pull request:

```sh
composer format
```

## Make a Pull Request

At this point, you should switch back to your master branch and make sure it's up to date with Laravel Loom Updates's master branch:

```sh
git remote add upstream git@github.com:mlusas/laravel-loom-updates.git
git checkout master
git pull upstream master
```

Then update your feature branch from your local copy of master, and push it!

```sh
git checkout 325-add-delete-functionality
git rebase master
git push --set-upstream origin 325-add-delete-functionality
```

Go to the [Laravel Loom Updates repo](https://github.com/mlusas/laravel-loom-updates) and press the "New pull request" button.

## Keeping your Pull Request updated

If a maintainer asks you to "rebase" your PR, they're saying that a lot of code has changed, and that you need to update your branch so it's easier to merge.

To learn more about rebasing in Git, there are a lot of [good](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) [resources](https://www.atlassian.com/git/tutorials/rewriting-history/git-rebase) but here's the suggested workflow:

```sh
git checkout 325-add-delete-functionality
git pull --rebase upstream master
git push --force-with-lease 325-add-delete-functionality
```

## Code review process

The core team looks at Pull Requests on a regular basis. After feedback has been given we expect responses within two weeks. After two weeks we may close the pull request if it isn't showing any activity.

## Thank you!

Thank you in advance for your contribution!