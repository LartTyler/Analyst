# Analyst
A PHP feature testing library inspired by GitHub's Scientist.

# Installation
```shell
$ composer require dbstudios/analyst
```

# How do I analyse?
Let's pretend that you have a large web application, and are in the process of changing the way you verify user
permissions when accessing a page.

```php
class MyController {
    public function isAllowed(User $user) {
        $result
    }
}
```