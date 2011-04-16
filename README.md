
# Server Example

<pre>
$pserver = new Naph\PTask\Server\Standard();
$pserver->listen( 3000 );
</pre>

# Client Example

<pre>
$jobs = array(
);

$prunner = new Naph\PTask\Runner\Standard();
$prunner->run( $jobs );

foreach ( $jobs as $job ) {
    print_r( $job->getResult() );
}
</pre>

# Job Example

# Amaxus Example

$job = new Naph\PTask\Job\Standard();
$job->type = 'processBlock';
$job->blockId = 123;

$prunner->run(array( $job ));

// $job->getResult() will return the processed block!
