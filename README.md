[![Build Status](https://travis-ci.org/LartTyler/Analyst.svg?branch=master)](https://travis-ci.org/LartTyler/Analyst)
[![Coverage Status](https://coveralls.io/repos/github/LartTyler/Analyst/badge.svg?branch=master)](https://coveralls.io/github/LartTyler/Analyst?branch=master)

[![Latest Stable Version](https://poser.pugx.org/dbstudios/analyst/v/stable)](https://packagist.org/packages/dbstudios/analyst)
[![Total Downloads](https://poser.pugx.org/dbstudios/analyst/downloads)](https://packagist.org/packages/dbstudios/analyst)
[![License](https://poser.pugx.org/dbstudios/analyst/license)](https://packagist.org/packages/dbstudios/analyst)

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
    public function isAllowed(User $user, $slug) {
        return Experiment::create('user-allowed')
            ->setContext([
                'user' => $user, // context ignores keys, however it's helpful to use keys to "flag" what each variable means
                'slug' => $slug,
            ])
            ->control(function(User $user, $slug) {
                return $user->isAdmin() || $user->getPermissions()->contains('view.page.' . $slug);
            })
            ->candidate(function(User $user, $slug) {
                return $user->getPermissionProvider()->isAccessAllowed($slug);
            })
            ->run();
    }
}
```

That's it! The `run` method of `Experiment` will always return the result from your control. Both your control and
your candidate will be run, and Analyst will record what happened to both blocks of code. In the next section, we'll
take a look at what you can do with that information.

# Publishing Experiment Results
Testing multiple code paths is pretty useless if we don't do anything with the information we get from our experiment.
That's where publishing comes into play. All you need to do is implement that `publish` method in a custom `Experiment`
class.

```php
class MyExperiment extends Experiment {
    private $db;
    private $stmt;

    public function __construct(PDO $db, $name = 'experiment') {
        parent::__construct($name);
        
        $this->db = $db;
        $this->stmt = $db->prepare('insert into experiment_results (name, timestamp, behavior, is_control,
            execution_time, result) values(:experiment, UTC_TIMESTAMP(), :behavior, :control, :duration, :result)');

        $this->stmt->bindValue(':experiment', $name);
    }

    public function publish(Result $result) {
        $behaviors = $result->getCandidates();
        
        array_unshift($candidates, $result->getControl());
        
        $this->db->beginTransaction();
        
        foreach ($behaviors as $behavior) {
            $this->stmt->bindValue(':behavior', $behavior->getName());
            $this->stmt->bindValue(':control', $behavior === $result->getControl(), PDO::PARAM_BOOL);
            $this->stmt->bindValue(':duration', $behavior->getDuration());
            $this->stmt->bindValue(':result', serialize($behavior->getResult());
            
            $this->stmt->execute();
        }
        
        $this->db->commit();
    }
    
    public static function create($db, $name = 'experiment') {
        return new MyExperiment($db, $name);
    }
}
```

Now, if we were to rewrite our earlier user permission example to use our custom `MyExperiment` class, it would look
like this.

```php
class MyController {
    public function isAllowed(User $user, $slug) {
        return MyExperiment::create($this->getConnection(), 'user-allowed')
            // set up context, control, and candidate...
            ->run();
    }
}
```

In the above example, we created our own `Experiment` implementation, named `MyExperiment`. In it, we made sure we had
a database connection (using PHP's `PDO` class), and wrote data to it using the `publish` method. Then, when we ran
our experiment within our controller example, all we needed to do was provide the extra arguments to the `create` static
method of `MyExperiment` (which we implemented ourselves to allow for the additional constructor argument).

After every experiment is run, Analyst will always call the `publish` method of the experiment used to do our analysis.
Our options aren't limited to simple database inserts. You might decide to log the data to a file using a library like
Monolog, or you might send it off to a cloud service. It's up to you!
