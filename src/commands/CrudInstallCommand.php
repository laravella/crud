<?php namespace Laravella\Crud;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudInstallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crud:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install database meta data for CRUD.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
            $this->call('config:publish',array('package'=>'laravella/crud'));
            $this->call('asset:publish',array('package'=>'laravella/crud'));
            $this->call('migrate',array('--package'=>'laravella/crud'));
            $this->info('Crud::migrations ran.');
            $this->call('db:seed',array('--class'=>'Laravella\\Crud\\PreJsonSeeder'));
            $this->call('db:seed',array('--class'=>'Laravella\\Crud\\CrudDatabaseSeeder'));
            $this->info('Crud::CrudDatabaseSeeder ran.');
            $this->call('db:seed',array('--class'=>'Laravella\\Crud\\PostJsonSeeder'));
            $this->info('CRUD installation complete.');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}