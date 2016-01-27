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
    public function isAllowed(User $user) {
        $result
    }
}
```
