<?php

namespace App\Form;

use App\Form\Type;
use Symfony\Component\Form\AbstractExtension;
use Silex\Application;

/**
 * Class FormExtension
 *
 * @package App\Form
 */
class FormExtension extends AbstractExtension
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * FormExtension constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    protected function loadTypes()
    {
        return array(
	        new Type\LoginType()
        );
    }
}
