<?php

namespace Laravella\Crud;

//use Illuminate\Console\Command;
use Illuminate\Database\Console\SeedCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class CrudRestoreCommand extends SeedCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crud:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a previous version of the meta data.';

    /**
     * The connection resolver instance.
     *
     * @var  \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * Create a new database seed command instance.
     *
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @return void
     */
    public function __construct(Resolver $resolver)
    {
        parent::__construct();

        $this->resolver = $resolver;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        $options = $this->option();

        $backId = isset($options['id']) ? $this->option('id') : null;

        parent::fire();

        //$this->call('db:seed', array('--class' => 'Laravella\\Crud\\CrudRestoreSeeder', '--id' => $backId));
        $this->info('CRUD restore complete.');
        $this->info('Restore complete.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('id', InputArgument::OPTIONAL, 'List all available backups.'),
            array('list', InputArgument::OPTIONAL, 'List all available backups.'),
            array('restore', InputArgument::OPTIONAL, 'Restore a specific backup, or the latest one if no id is specified.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder', 'DatabaseSeeder'),
            array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed'),
            array('id', 'i', InputOption::VALUE_OPTIONAL, 'The id of the backup to restore. Use list argument to list available ids.', null)
        );
    }

}