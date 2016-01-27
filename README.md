# Analyst
A PHP feature testing library inspired by GitHub's Scientist.

Please be aware, this README is a work in progress, and may contain incomplete information.

# Installation
```shell
$ composer require dbstudios/analyst
```

# How do I analyze?
Let's pretend that you have a large web application, and are in the process of changing the way you verify user
permissions when accessing a page.

```php
class MyController {
    public function isAllowed(User $user, $page) {
        return Experiment::create('user-allowed')
            ->setContext([
                'user' => $user, // context ignores keys, however it's helpful to use keys to "flag" what each variable means
                'page' => $page,
            ])
            ->control(function(User $user, $page) {
                return $user->isAdmin() || $user->getPermissions()->contains('access.' . $page);
            })
            ->candidate(function(User $user, $page) {
                return $user->getPermissionProvider()->isAccessAllowed($page);
            })
            ->run();
    }
}
```

That's it! The `run` method of `Experiment` will always return the result from your control. Both your control and
your candidate will be run, and Analyst will record what happened to both blocks of code. In the next section, we'll
take a look at what you can do with that information.
