<?php

// Author: John Doe
// License: MIT
// Source: https://github.com/rashidlaasri/LaravelInstaller
// Note: This file has been modified to be included directly in the software rather than via a composer package

namespace App\Http\Controllers\Install;

use Illuminate\Routing\Controller;
use App\Helpers\Install\RequirementsChecker;

class RequirementsController extends Controller
{
    /**
     * @var RequirementsChecker
     */
    protected $requirements;

    /**
     * @param RequirementsChecker $checker
     */
    public function __construct(RequirementsChecker $checker)
    {
        $this->requirements = $checker;
    }

    /**
     * Display the requirements page.
     *
     * @return \Illuminate\View\View
     */
    public function requirements()
    {
        $phpSupportInfo = $this->requirements->checkPHPversion(
            config('installer.core.minPhpVersion')
        );
        $requirements = $this->requirements->check(
            config('installer.requirements')
        );

        return view('install.requirements', compact('requirements', 'phpSupportInfo'));
    }
}
